<?php

namespace App\Http\Controllers;

use App\Events\NewOrder;
use App\Http\Requests\Order\AddOrderRequest;
use App\Http\Requests\Order\EditOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Branch;
use App\Models\ExtraIngredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Repo;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    public function store(AddOrderRequest $request)
    {
        $request->validated($request->all());

        $totalPrice = 0;

        foreach($request->products as $productData) {
            $product = Product::find($productData['id']);

            $productSubtotal = $product->price * $productData['qty'];
            
            $totalPrice += $productSubtotal;

            if(isset($productData['extraIng'])) {

                foreach($productData['extraIng'] as $ingredientData) {
                    $ingredient = ExtraIngredient::find($ingredientData['id']);

                    $totalPrice += $ingredient->price_per_piece;
                }
            }

        }
        $order = Order::create(array_merge($request->except(['total_price']),
        [ 'time' => Carbon::now()->format('H:i:s') ]
        ));
        $orderTax = intval($order->branch->taxRate) / 100;
        
        $order->total_price = $totalPrice + ($totalPrice * $orderTax);
        
        $order->save();


        event(new NewOrder($order));

        return $this->apiResponse(new OrderResource($order),'Data saved Successfully',201);
    }

    public function update(EditOrderRequest $request , Order $order)
    {
        // $request->validated($request->all());
        
        $order->delete();

        $order = Order::create(array_merge($request->except('time', 'is_update' ,'total_price'),
            ['time' => Carbon::now()->format('H:i:s'),
            'is_update' => 1 ,
            'total_price' => $order->calculate($request,$order)]
        ));

        event(new NewOrder($order));

        return $this->apiResponse(OrderResource::make($order),'Data saved Successfully',200);
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



}
