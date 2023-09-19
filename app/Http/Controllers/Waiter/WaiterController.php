<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Branch;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterController extends Controller
{ 
    use ApiResponseTrait;
    public function getOrder(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('status',3)->where('time_Waiter',null)->get();
        return $this->apiResponse(OrderResource::collection($orders), 'This orders are Done', 200);
    }

    public function done(Order $order)
    {
        if($order->status == 3){
            $order->update(['time_Waiter' => Carbon::now()->format("H:i:s"),
            'author' => Auth::user()->email]);
            return $this->apiResponse($order,'success',200);
        }
    }
}
