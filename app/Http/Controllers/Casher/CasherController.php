<?php

namespace App\Http\Controllers\Casher;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderProductResource;
use App\Models\Order;
use Illuminate\Http\Request;

class CasherController extends Controller
{
    use ApiResponseTrait;

    public function getOrders()
    {
        $orders = Order::where('status',3)->where('is_paid',0)->get();
        return $this->apiResponse(OrderProductResource::collection($orders),'success',200);
    }
    public function ChangeToPaid(Order $order)
    {
        if ($order->status == 3 && $order->is_paid == 0) {

            $order->update([
                'is_paid' => 1
            ]);

            return $this->apiResponse($order, ' Payment status changed successfully', 201);
        }

    }
}
