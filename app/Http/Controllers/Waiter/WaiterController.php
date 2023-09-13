<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WaiterController extends Controller
{ 
    use ApiResponseTrait;


    public function done(Order $order)
    {
        if($order->status == 3){
            $order->update(['time_Waiter' => Carbon::now()->format("H:i:s")]);
            return $this->apiResponse($order,'success',200);
        }
    }
}
