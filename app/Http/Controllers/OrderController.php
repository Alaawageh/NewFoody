<?php

namespace App\Http\Controllers;

use App\Events\NewOrder;
use App\Http\Requests\Order\AddOrderRequest;
use App\Http\Requests\Order\EditOrderRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\OrderResource;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\ExtraIngredient;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductExtraIngredient;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\Table;
use App\Types\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function show(Order $order) {
        return $this->apiResponse(OrderResource::make($order),'success',200);
    }

    public function getByBranch(Branch $branch)
    {
        $orders = $branch->order()->get();
        return $this->apiResponse(OrderResource::collection($orders),'success',200);

    }

    public function getByTable(Table $table)
    {
        $orders = $table->order()->get();
        return $this->apiResponse(OrderResource::collection($orders),'success',200);

    }
    public function store(AddOrderRequest $request)
    {
        DB::beginTransaction();
        try{
            $order = Order::where('table_id',$request->table_id)->where('branch_id',$request->branch_id)->where('is_paid',0)->latest()->first();
            if (! $order) {
                $bill = Bill::create([
                    'price' => 0 ,
                    'is_paid' => 0 
                ]);
                $order = Order::create([
                    'status' => OrderStatus::BEFOR_PREPARING,
                    'is_paid' => 0,
                    'is_update' => 0,
                    'time' => Carbon::now()->format('H:i:s'),
                    'table_id' => $request['table_id'],
                    'branch_id' => $request['branch_id'],
                    'bill_id' => $bill->id,
                    
                ]);
                $totalPrice = 0;
                foreach ($request->products as $productData) {
                    $product = Product::find($productData['product_id']);
                    $estimatedTimesInSeconds = [];
                    $estimated = \Carbon\Carbon::parse($product['estimated_time']);
                    $estimatedTimesInSeconds[] = $estimated;
                    $orderProduct = OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $product['id'],
                        'qty' => $productData['qty'],
                        'note' => $productData['note'],
                        'subTotal' => $product['price'] * $productData['qty']
                    ]);
                    $totalPrice += $orderProduct['subTotal'];
                    if(isset($productData['removedIngredients'])) {
                        foreach($productData['removedIngredients'] as $removedIngredientData) {
                            $ing = Ingredient::find($removedIngredientData['remove_id']);
                            $orderProduct->ingredients()->attach($ing->id);
                            
                        }
                    }
                    
                    if(isset($productData['extraIngredients'])) {
                        foreach($productData['extraIngredients'] as $ingredientData) {
    
                            $extraingredient = ExtraIngredient::find($ingredientData['ingredient_id']);
                            $qtyExtra = ProductExtraIngredient::where('product_id',$product->id)->where('extra_ingredient_id',$extraingredient->id)->first();
                            $sub = $qtyExtra['price_per_piece'] * $productData['qty'];
                           
                            OrderProductExtraIngredient::create([
                                'order_product_id' => $orderProduct->id,
                                'extra_ingredient_id' => $extraingredient['id'],
                               
                            ]); 
                            $totalPrice += $sub;
                        }
                    }
                }
                $maxEstimatedTimeInSeconds = max($estimatedTimesInSeconds);
                $maxEstimatedTimeFormatted =  \Carbon\Carbon::parse($maxEstimatedTimeInSeconds)->format("H:i:s");
                $order->estimatedForOrder = $maxEstimatedTimeFormatted;
                
                $orderTax = (intval($order->branch->taxRate) / 100);
               
                $order->total_price = $totalPrice + ($totalPrice * $orderTax);
                
                $order->save();
                $bill->update([
                    'price' => $order->total_price,
                    'is_paid' => $order->is_paid,
                ]);
                
                event(new NewOrder($order));
                DB::commit();
                return $this->apiResponse(($order),'Data Saved successfully',201);
            }else{
                $bill = $order->bill_id;
                $order = Order::create([
                    'status' => OrderStatus::BEFOR_PREPARING,
                    'is_paid' => 0,
                    'is_update' => 0,
                    'time' => Carbon::now()->format('H:i:s'),
                    'table_id' => $request['table_id'],
                    'branch_id' => $request['branch_id'],
                    'bill_id' => $bill,
                ]);
            
                $totalPrice = 0;
                $estimatedTimesInSeconds = [];
            
                foreach ($request->products as $productData) {
                    $product = Product::find($productData['product_id']);
                    $estimated = \Carbon\Carbon::parse($product['estimated_time']);
                    $estimatedTimesInSeconds[] = $estimated;
            
                    $x = OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $product['id'],
                        'qty' => $productData['qty'],
                        'note' => $productData['note'],
                        'subTotal' => $product['price'] * $productData['qty']
                    ]);
            
                    $totalPrice += $x['subTotal'];
                    if(isset($productData['removedIngredients'])) {
                        foreach($productData['removedIngredients'] as $removedIngredientData) {
                            $ing = Ingredient::find($removedIngredientData['remove_id']);
                            $x->ingredients()->attach($ing->id);
                            
                        }
                    }
            
                    if(isset($productData['extraIngredients'])) {
                        foreach($productData['extraIngredients'] as $ingredientData) {
                            $extraingredient = ExtraIngredient::find($ingredientData['ingredient_id']);
                            $qtyExtra = ProductExtraIngredient::where('product_id',$product->id)->where('extra_ingredient_id',$extraingredient->id)->first();
                            $sub = $qtyExtra['price_per_piece'] * $productData['qty'];
                            OrderProductExtraIngredient::create([
                                'order_product_id' => $x->id,
                                'extra_ingredient_id' => $extraingredient['id'],
                            ]); 
                            $totalPrice += $sub;
                        }
                    }
                }
            
                $maxEstimatedTimeInSeconds = max($estimatedTimesInSeconds);
                $maxEstimatedTimeFormatted =  \Carbon\Carbon::parse($maxEstimatedTimeInSeconds)->format("H:i:s");
                $order->estimatedForOrder = $maxEstimatedTimeFormatted;
                
                $orderTax = (intval($order->branch->taxRate) / 100);
                $order->total_price = $totalPrice + ($totalPrice * $orderTax);
                $order->save();
                $billOrder = Bill::where('id',$order->bill_id)->where('is_paid',0)->first();
                $billOrder->update([
                'price' =>$billOrder->price + $order->total_price,
                'is_paid' => $order->is_paid,
                ]); 
                event(new NewOrder($order));
                DB::commit();
                return $this->apiResponse(($order),'Data Saved successfully',201);
            }

        }catch(\Exception $e){
            DB::rollBack();
            return response(['error' => $e->getMessage()],400);
        }

    }

    public function update(EditOrderRequest $request , Order $order)
    {
        if($order->status == 1 && $order->is_paid == 0) {
            DB::beginTransaction();
            $bill = $order->bill_id;
            
            $billOrder = Bill::where('id',$bill)->where('is_paid',0)->first();
            $billOrder->update([
                'price' =>$billOrder->price - $order->total_price,
            ]);
            $order->delete();
            try{
                $order = Order::create([
                    'status' => OrderStatus::BEFOR_PREPARING,
                    'is_paid' => 0,
                    'is_update' => 1,
                    'time' => Carbon::now()->format('H:i:s'),
                    'table_id' => $request['table_id'],
                    'branch_id' => $request['branch_id'],
                    'bill_id' => $bill,
                ]);
                $totalPrice = 0;
                $estimatedTimesInSeconds = [];
            
                foreach ($request->products as $productData) {
                    $product = Product::find($productData['product_id']);
                    $estimated = \Carbon\Carbon::parse($product['estimated_time']);
                    $estimatedTimesInSeconds[] = $estimated;
            
                    $x = OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $product['id'],
                        'qty' => $productData['qty'],
                        'note' => $productData['note'],
                        'subTotal' => $product['price'] * $productData['qty']
                    ]);
            
                    $totalPrice += $x['subTotal'];
                    if(isset($productData['removedIngredients'])) {
                        foreach($productData['removedIngredients'] as $removedIngredientData) {
                            $ing = Ingredient::find($removedIngredientData['remove_id']);
                            $x->ingredients()->attach($ing->id);
                            
                        }
                    }
            
                    if(isset($productData['extraIngredients'])) {
                        foreach($productData['extraIngredients'] as $ingredientData) {
                            $extraingredient = ExtraIngredient::find($ingredientData['ingredient_id']);
                            $qtyExtra = ProductExtraIngredient::where('product_id',$product->id)->where('extra_ingredient_id',$extraingredient->id)->first();
                            $sub = $qtyExtra['price_per_piece'] * $productData['qty'];
                            OrderProductExtraIngredient::create([
                                'order_product_id' => $x->id,
                                'extra_ingredient_id' => $extraingredient['id'],
                            ]); 
                            $totalPrice += $sub;
                        }
                    }
                }
            
                $maxEstimatedTimeInSeconds = max($estimatedTimesInSeconds);
                $maxEstimatedTimeFormatted =  \Carbon\Carbon::parse($maxEstimatedTimeInSeconds)->format("H:i:s");
                $order->estimatedForOrder = $maxEstimatedTimeFormatted;
                
                $orderTax = (intval($order->branch->taxRate) / 100);
                $order->total_price = $totalPrice + ($totalPrice * $orderTax);
                $order->save();
                $billOrder = Bill::where('id',$order->bill_id)->where('is_paid',0)->first();
                $billOrder->update([
                'price' =>$billOrder->price + $order->total_price,
                'is_paid' => $order->is_paid,
                ]);
                DB::commit();
                return $this->apiResponse(($order),'Data Saved successfully',201);
            } catch (\Exception $e) {
                DB::rollback();
                throw new \Exception($e->getMessage());
            }
        }
    }
    

    public function delete(Order $order)
    {
        $order->delete();

        return $this->apiResponse(null,'Data Deleted' , 200);
    }

    public function getOrderForEdit(Request $request)
    {
        $order = Order::where('table_id',$request->table_id)->where('branch_id',$request->branch_id)->where('is_paid',0)->latest()->first();
        if($order && $order->status == 1 ) {
            return $this->apiResponse(OrderResource::make($order),'success',200);
        }elseif($order){
            return $this->apiResponse(($order),'This order is under preparation',200);
        }else{
            return $this->apiResponse(null,'Not Found',404);

        }
    } 

    public function getOrderforRate(Branch $branch,Table $table)
    {
        
        $bill = Bill::where('is_paid',0)->whereHas('order', fn ($query) => 
            $query->where('table_id', $table->id)->where('branch_id', $branch->id)->where('is_paid',0)->where('serviceRate',null)
        )
        ->latest()->first();
        if($bill) {
            $orderProduct = $bill->order()->with('product')->get()->pluck('product')->unique();
       
            $collection = $orderProduct->map(function ($array) {
                return collect($array)->unique('id');
            });
            $uniqueProducts = $collection->flatten(1)->unique('id')->values();
            $response = [
                "bill_id" => $bill->id,
                "products" => $uniqueProducts->toArray()
            ];
            return $this->apiresponse($response,'done',200);
        }

    }

    
    public function AddRate(Request $request,Bill $bill) {
        $validator = Validator::make($request->all(), [
            'feedback' => 'nullable|string',
            'serviceRate' => 'nullable|integer|between:1,5',
        ]);
        if($bill->is_paid == 0) {
            $orders = $bill->order()->where('bill_id',$bill->id)->where('is_paid',0)->get();
            foreach($orders as $order){
                $order->update([
                    'serviceRate' => $request->serviceRate,
                    'feedback' => $request->feedback
                ]);
            }

        return $this->apiResponse(BillResource::make($bill),'Saved Successfully',201);
        }

    }

    public function createOrder($request, $order)
    {
        $bill = Bill::create([
            'price' => 0 ,
            'is_paid' => 0 
        ]);
        $order = Order::create([
            'status' => OrderStatus::BEFOR_PREPARING,
            'is_paid' => 0,
            'is_update' => 1,
            'time' => Carbon::now()->format('H:i:s'),
            'table_id' => $request['table_id'],
            'branch_id' => $request['branch_id'],
            'bill_id' => $bill->id,
        ]);
    
        $totalPrice = 0;
        $estimatedTimesInSeconds = [];
    
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);
            $estimated = \Carbon\Carbon::parse($product['estimated_time']);
            $estimatedTimesInSeconds[] = $estimated;
    
            $x = OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'qty' => $productData['qty'],
                'note' => $productData['note'],
                'subTotal' => $product['price'] * $productData['qty']
            ]);
    
            $totalPrice += $x['subTotal'];
            if(isset($productData['removedIngredients'])) {
                foreach($productData['removedIngredients'] as $removedIngredientData) {
                    $ing = Ingredient::find($removedIngredientData['remove_id']);
                    $x->ingredients()->attach($ing->id);
                    
                }
            }
    
            if(isset($productData['extraIngredients'])) {
                foreach($productData['extraIngredients'] as $ingredientData) {
                    $extraingredient = ExtraIngredient::find($ingredientData['ingredient_id']);
                    $qtyExtra = ProductExtraIngredient::where('product_id',$product->id)->where('extra_ingredient_id',$extraingredient->id)->first();
                    $sub = $qtyExtra['price_per_piece'] * $productData['qty'];
                    OrderProductExtraIngredient::create([
                        'order_product_id' => $x->id,
                        'extra_ingredient_id' => $extraingredient['id'],
                    ]); 
                    $totalPrice += $sub;
                }
            }
        }
    
        $maxEstimatedTimeInSeconds = max($estimatedTimesInSeconds);
        $maxEstimatedTimeFormatted =  \Carbon\Carbon::parse($maxEstimatedTimeInSeconds)->format("H:i:s");
        $order->estimatedForOrder = $maxEstimatedTimeFormatted;
        
        $orderTax = (intval($order->branch->taxRate) / 100);
        $order->total_price = $totalPrice + ($totalPrice * $orderTax);
        $order->save();
        $bill->update([
            'price' => $order->total_price,
            'is_paid' => $order->is_paid,
        ]);
        event(new NewOrder($order));

    }

}
