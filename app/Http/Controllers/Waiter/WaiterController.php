<?php

namespace App\Http\Controllers\Waiter;

use App\Events\CallWaiter;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TableResource;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterController extends Controller
{ 
    use ApiResponseTrait;
    public function getOrder(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('table_id','!=',1111)->where('is_paid',0)->where('status',3)->where('author',null)->get();
        return $this->apiResponse(OrderResource::collection($orders), 'This orders are Done', 200);
    }

    public function done(Request $request , Order $order)
    {
        if($order->status == 3 && isset($request->waiter_name)){
            $order->update(['time_Waiter' => now(),
            'author' => $request->waiter_name]);
            return $this->apiResponse($order,'success',200);
        }
    }

    public function callWaiter(Request $request)
    {
        $table = Table::find($request->table_id);
        $branch = Branch::find($request->branch_id);
        event(new CallWaiter($table,$branch));
        return $this->apiResponse(TableResource::make($table), 'Called successfully', 200);
        
    }
}
