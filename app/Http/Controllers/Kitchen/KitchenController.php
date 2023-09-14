<?php

namespace App\Http\Controllers\Kitchen;

use App\Events\ToCasher;
use App\Events\ToWaiter;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\ExtraIngredient;
use App\Models\Order;
use App\Models\Repo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    use ApiResponseTrait;

    public function getOrders()
    {
        $orders = Order::with(['products', 'products.extra'])->where('status',1)->get();
        if($orders) {
        return $this->apiResponse(OrderResource::collection($orders), 'No Order Befor_Preparing', 200);

        }
    }

    public function getToPreparing()
    {
        $orders = Order::with(['products', 'products.extra'])->where('status',2)->get();
        return $this->apiResponse(OrderResource::collection($orders), 'This orders are Preparing', 200);  
    }

    public function ChangeToPreparing(Order $order)
    {
        if ($order->status = '1') {
            $order->update(['status' => '2']);
            $order->save();
        }
        return $this->apiResponse($order->load(['products', 'products.extra']), 'Changes saved successfully', 200);

    } 
    public function getToDone()
    {
        $orders = Order::with(['products', 'products.extra'])->where('status',2)->get();
        return $this->apiResponse(OrderResource::collection($orders), 'This orders are Perparing', 200);  
    }

    public function ChangeToDone(Order $order)
    { 
        if ($order->status = '2'){
            $order->update([
                'status' => '3',
                'time_end' => Carbon::now(),
            ]);
            $order->save();
            foreach($order['products'] as $product) {
                foreach ($product->ingredients as $ingredient) {
                    $ingredient->total_quantity -= $ingredient->pivot->quantity * $product->pivot->qty;
                    $ingredient->save();
                }

            }
            event(new ToCasher($order));
            event(new ToWaiter($order));
            return $this->apiResponse($order, 'Changes saved successfully', 201);
        }
    }

    public function delete(Order $order)
    {
        if ($order->status == 1){
            $order->delete();
            return $this->apiResponse(null,'Deleted Successfully',200);
        }

    }
}
