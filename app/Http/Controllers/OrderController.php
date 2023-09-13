<?php

namespace App\Http\Controllers;

use App\Events\NewOrder;
use App\Http\Requests\Order\AddOrderRequest;
use App\Http\Requests\Order\EditOrderRequest;
use App\Http\Resources\OrderProductExtraIngResource;
use App\Http\Resources\OrderProductResource;
use App\Http\Resources\OrderResource;
use App\Models\Branch;
use App\Models\ExtraIngredient;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductExtra;
use App\Models\OrderProductExtraIngredient;
use App\Models\Product;
use App\Models\Repo;
use App\Models\Table;
use App\Types\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $orders = OrderResource::collection(Order::get());
        return $this->apiResponse($orders,'success',200);
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
        'table_id' => 'required|exists:tables,id',
        'branch_id'=> 'exists:branches,id',
        ]);
        $order = Order::create([
            'status' => OrderStatus::BEFOR_PREPARING,
            'is_paid' => 0,
            'is_update' => 0,
            'time' => Carbon::now()->format('H:i:s'),
            'table_id' => $v['table_id'],
            'branch_id' => $v['branch_id']
        ]);
        $totalPrice = 0;
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);
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
                    $total = ($product['price'] * $productData['qty']) + $extraingredient['price_per_peice'];

                    OrderProductExtraIngredient::create([
                        'order_id' => $order->id,
                        'product_id' => $product['id'],
                        'extra_ingredient_id' => $extraingredient['id'],
                        'total' => $total,
                    ]); 
                    $totalPrice = $total;
                }
            }
        }
        $orderTax = (intval($order->branch->taxRate) / 100);
        
        $order->total_price = $totalPrice + ($totalPrice * $orderTax);
        
        $order->save();
        event(new NewOrder($order));
        return $this->apiResponse(new OrderProductResource($order), 'Done', 201);

    }

    public function update(Request $request , Order $order)
    {
        $v = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'branch_id'=> 'exists:branches,id',
        ]);
        if($order->status == 1) {
            $order->delete();

            $order = Order::create([
                'status' => OrderStatus::BEFOR_PREPARING,
                'is_paid' => 0,
                'is_update' => 1,
                'time' => Carbon::now()->format('H:i:s'),
                'table_id' => $v['table_id'],
                'branch_id' => $v['branch_id']
            ]);
            $totalPrice = 0;
            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
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
                        $total = ($product['price'] * $productData['qty']) + $extraingredient['price_per_peice'];
    
                        OrderProductExtraIngredient::create([
                            'order_id' => $order->id,
                            'product_id' => $product['id'],
                            'extra_ingredient_id' => $extraingredient['id'],
                            'total' => $total,
                        ]); 
                        $totalPrice = $total;
                    }
                }
            }
            $orderTax = (intval($order->branch->taxRate) / 100);
            
            $order->total_price = $totalPrice + ($totalPrice * $orderTax);
            
            $order->save();
            event(new NewOrder($order));

        }
        return $this->apiResponse(new OrderProductResource($order),'Data saved Successfully',200);
    }

    public function delete(Order $order)
    {
        $order->delete();

        return $this->apiResponse(null,'Data Deleted' , 200);
    }

    public function getOrderForEdit(Request $request)
    {
        $order = Order::where('table_id',$request->table_id)->where('status','1')->latest()->first();
        if(isset ($order) ) {
            return $this->apiResponse(OrderResource::make($order),'success',200);
        }
        return $this->apiResponse(null,'This order is under preparation',404);
    } 

    public function getOrderforRate(Table $table) {
        $order = Order::where('table_id',$table->id)->where('status',3)->where('serviceRate',null)->latest()->first();
        if($order)
        {
            return $this->apiResponse(OrderResource::make($order),'Done',200);
        }
        return $this->apiResponse(null,'Not found',200);
    }

    public function storeRate(Request $request,Order $order) {
        $validator = Validator::make($request->all(), [
            'feedback' => 'nullable|string',
            'serviceRate' => 'nullable|integer|between:1,5',
        ]);
        $serviceRate = $order->update([
            'serviceRate' => $request->serviceRate,
            'feedback' => $request->feedback
        ]);
        return $this->apiResponse($order,'Saved Successfully',201);
    }



}
