<?php

namespace App\Http\Controllers\Kitchen;

use App\Events\IngredientMin;
use App\Events\ToCasher;
use App\Events\ToWaiter;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Carbon\Carbon;

class KitchenController extends Controller
{
    use ApiResponseTrait;

    public function getOrders()
    {
        $orders = Order::where('status',1)->get();
        if($orders) {
        return $this->apiResponse(OrderResource::collection($orders), 'No Order Befor_Preparing', 200);

        }
    }

    public function getToPreparing()
    {
        $orders = Order::where('status',2)->get();
        return $this->apiResponse(OrderResource::collection($orders), 'This orders are Preparing', 200);  
    }

    public function ChangeToPreparing(Order $order)
    {
        if ($order->status = '1') {
            $order->update(['status' => '2']);
            $order->save();
        }
        return $this->apiResponse($order, 'Changes saved successfully', 200);

    } 
    public function getToDone()
    {
        $orders = Order::where('status',2)->get();
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
            
            foreach($order->product as $one) {
                if($one->extraIngredients) {
                    foreach($one->extraIngredients as $productExtra){
                        $qtyExtra = $productExtra->pivot->quantity;
                    }
                }
                foreach ($one->ingredients as $ingredient) {
                    $ingredient->total_quantity -= ($ingredient->pivot->quantity * $one->pivot->qty) -  ($qtyExtra * $one->pivot->qty);
                    $ingredient->save();
                    // return $ingredient;
                    // if($ingredient === $ingredient->threshold) {
                    //     event(new IngredientMin($ingredient));
                    // }
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
