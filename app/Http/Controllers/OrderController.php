<?php

namespace App\Http\Controllers;

use App\Events\NewOrder;
use App\Http\Resources\AddOrderResource;
use App\Http\Resources\OrderResource;
use App\Models\Branch;
use App\Models\ExtraIngredient;
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

    public function index()
    {
        $orders = Order::get();
        return $this->apiResponse(OrderResource::collection($orders),'success',200);
    }

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
    public function store(Request $request)
    {

        $v = $request->validate([
            'table_id' => 'exists:tables,id',
            'branch_id'=> 'exists:branches,id',
            'products.*.product_id' => 'exists:products,id',
            'products.*.extraIngredients.*.ingredient_id' => 'exists:extra_ingredients,id',
            ]);
        DB::beginTransaction();
        try{
            $order = Order::create([
                'status' => OrderStatus::BEFOR_PREPARING,
                'is_paid' => 0,
                'is_update' => 0,
                'time' => Carbon::now()->format('H:i:s'),
                'table_id' => $request['table_id'],
                'branch_id' => $request['branch_id']
            ]);
            $totalPrice = 0;
            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                $estimatedTimesInSeconds = [];
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
                if(isset($productData['extraIngredients'])) {
                    foreach($productData['extraIngredients'] as $ingredientData) {
                        
                        $extraingredient = ExtraIngredient::find($ingredientData['ingredient_id']);
                        $qtyExtra = ProductExtraIngredient::where('product_id',$product->id)->where('extra_ingredient_id',$extraingredient->id)->get();
                        foreach($qtyExtra as $one){
                            
                            $total = ($product['price'] * $productData['qty']) + ($extraingredient['price_per_kilo'] * $one->quantity)/1000;
    
                        }
                        OrderProductExtraIngredient::create([
                            'order_product_id' => $x->id,
                            'extra_ingredient_id' => $extraingredient['id'],
                            'total' => $total,
                        ]); 
                        $totalPrice = $total;
                    }
                }
            }
            $maxEstimatedTimeInSeconds = max($estimatedTimesInSeconds);
            $maxEstimatedTimeFormatted =  \Carbon\Carbon::parse($maxEstimatedTimeInSeconds)->format("H:i:s");
            $order->estimatedForOrder = $maxEstimatedTimeFormatted;
            
            $orderTax = (intval($order->branch->taxRate) / 100);
           
            $order->total_price = $totalPrice + ($totalPrice * $orderTax);
            
            
            $order->save();
            
            event(new NewOrder($order));
            DB::commit();
            return $this->apiResponse(new AddOrderResource($order),'Data Saved successfully',201);
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }

    }

    public function update(Request $request , Order $order)
    {
        if($order->status == 1) {
            DB::beginTransaction();
            $order->delete();

            
            try{
                $order = Order::create([
                    'status' => OrderStatus::BEFOR_PREPARING,
                    'is_paid' => 0,
                    'is_update' => 1,
                    'time' => Carbon::now()->format('H:i:s'),
                    'table_id' => $request['table_id'],
                    'branch_id' => $request['branch_id']
                ]);
                $totalPrice = 0;
                foreach ($request->products as $productData) {
                    $product = Product::find($productData['product_id']);
                    $estimatedTimesInSeconds = [];
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
                    if(isset($productData['extraIngredients'])) {
                        foreach($productData['extraIngredients'] as $ingredientData) {
                            
                            $extraingredient = ExtraIngredient::find($ingredientData['ingredient_id']);
                            $qtyExtra = ProductExtraIngredient::where('product_id',$product->id)->where('extra_ingredient_id',$extraingredient->id)->get();
                            foreach($qtyExtra as $one){
                                
                                $total = ($product['price'] * $productData['qty']) + ($extraingredient['price_per_kilo'] * $one->quantity)/1000;
        
                            }
                            OrderProductExtraIngredient::create([
                                'order_product_id' => $x->id,
                                'extra_ingredient_id' => $extraingredient['id'],
                                'total' => $total,
                            ]); 
                            $totalPrice = $total;
                        }
                    }
                }
                $maxEstimatedTimeInSeconds = max($estimatedTimesInSeconds);
                $maxEstimatedTimeFormatted =  \Carbon\Carbon::parse($maxEstimatedTimeInSeconds)->format("H:i:s");
                $order->estimatedForOrder = $maxEstimatedTimeFormatted;
                
                $orderTax = (intval($order->branch->taxRate) / 100);
                
                $order->total_price = $totalPrice + ($totalPrice * $orderTax);
                
                $order->save();

                event(new NewOrder($order));

                DB::commit();

                return $this->apiResponse(new AddOrderResource($order),'Data Saved successfully',201);
            }catch(\Exception $e){
                DB::rollBack();
                return redirect()->back()->with(['error' => $e->getMessage()]);
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
        $order = Order::where('table_id',$request->table_id)->where('branch_id',$request->branch_id)->latest()->first();
        if($order && $order->status == 1 ) {
            return $this->apiResponse(OrderResource::make($order),'success',200);
        }
        return $this->apiResponse($order,'This order is under preparation',404);
    } 

    public function getOrderforRate(Branch $branch,Table $table) {
        $order = Order::where('branch_id',$branch->id)->where('table_id',$table->id)->where('status',3)
        ->where('serviceRate',null)->where('is_paid',0)
        ->get();
        if($order)
        {
            return $this->apiResponse(OrderResource::collection($order),'Done',200);
        }
        return $this->apiResponse(null,'Not found',200);
    }

    public function storeRate(Request $request,Order $order) {
        $validator = Validator::make($request->all(), [
            'feedback' => 'nullable|string',
            'serviceRate' => 'nullable|integer|between:1,5',
        ]);
        $order->update([
            'serviceRate' => $request->serviceRate,
            'feedback' => $request->feedback
        ]);
        return $this->apiResponse($order,'Saved Successfully',201);
    }



}
