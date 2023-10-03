<?php

namespace App\Http\Controllers\Casher;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Http\Resources\OrderProductResource;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;

class CasherController extends Controller
{
    use ApiResponseTrait;

    public function getOrders(Branch $branch)
    {
        $bill = Bill::where('is_paid',0)->whereHas('order', fn ($query) => 
            $query->where('branch_id', $branch->id)->where('is_paid',0)->where('status', 3)
        )
        ->get();
        return $this->apiResponse(BillResource::collection($bill),'Done',200);

    }
    public function ChangeToPaid(Bill $bill)
    {
        if($bill->is_paid == 0)
        {
            $orders = $bill->order;
            foreach($orders as $order){
                $order->update([
                    'is_paid' => 1,
                ]);
            }

            $bill->update([
                'is_paid' => 1,
            ]);
            return $this->apiResponse(BillResource::make($bill),'Done',200);
        }
    }
    
}
