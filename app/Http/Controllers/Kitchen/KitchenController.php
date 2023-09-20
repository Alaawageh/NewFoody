<?php

namespace App\Http\Controllers\Kitchen;

use App\Events\IngredientMin;
use App\Events\ToCasher;
use App\Events\ToWaiter;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Branch;
use App\Models\Order;
use Carbon\Carbon;

class KitchenController extends Controller
{
    use ApiResponseTrait;

    public function getOrders(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('status',1)->get();
        if($orders) {
        return $this->apiResponse(OrderResource::collection($orders), 'this Orders are Befor_Preparing', 200);

        }
        return $this->apiResponse(null, 'No Order Befor_Preparing', 404);

    }

    public function ChangeToPreparing(Order $order)
    {
        if ($order->status = 1) {
            $order->update([
                'status' => 2,
                'time_start' => now()
            ]);
            $order->save();
            return $this->apiResponse($order, 'Changes saved successfully', 200);

        }

    } 
    public function getToDone(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('status',2)->get();
        if($orders){
            return $this->apiResponse(OrderResource::collection($orders), 'This orders are Perparing', 200);  

        }
        return $this->apiResponse($orders, 'Not Found', 404);  

    }

    public function ChangeToDone(Order $order)
    { 
        if ($order->status = '2'){
            $order->update([
                'status' => '3',
                'time_end' => now(),
            ]);
            $order->save();
            
            // foreach($order->product as $one) {
            //     if($one->extraIngredients) {
            //         foreach($one->extraIngredients as $productExtra){
            //             $qtyExtra = $productExtra->pivot->quantity;
            //             // $priceExtra = $productExtra->pivot->price_per_piece;
            //         }
            //     }
            //     foreach ($one->ingredients as $ingredient) {
            //         $ingredient->total_quantity -= ($ingredient->pivot->quantity * $one->pivot->qty) -  ($qtyExtra * $one->pivot->qty);
            //         $ingredient->save();
            //         // return $ingredient;
            //         // if($ingredient === $ingredient->threshold) {
            //         //     event(new IngredientMin($ingredient));
            //         // }
            //     }

            // }
            // foreach($order->products) {

            // }
            foreach($order->product as $one) {
                $qty = $one->pivot->qty;

                foreach($one->ingredients as $ingredient) {
                    $quantity = $ingredient->pivot->quantity;
                    if($one->extraIngredients) {
                        foreach($one->extraIngredients as $extra) {
                            $proExtra = $extra->pivot->quantity;
                        }
                        $ingredient->total_quantity -= ($quantity * $qty - $proExtra * $qty ) ;
                        $ingredient->save();
                    }else{
                        $ingredient->total_quantity -= $quantity * $qty;
                        $ingredient->save();
                    }

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
