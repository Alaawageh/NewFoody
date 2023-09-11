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
        $orders = Order::where('status','1')->get();
        return $this->apiResponse(OrderResource::collection($orders), 'This orders are Befor_Preparing', 200);
    }

    public function ChangeToPreparing(Order $order)
    {
        if ($order->status = '1') {
            $order->update(['status' => '2']);
            $order->save();
        }
        return $this->apiResponse($order, 'Changes saved successfully', 200);

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
                $PROQTY = $product['qty'];
                foreach($product['extraIng'] as $ExtraIngredientData) {
                    $ExtraIngredient = ExtraIngredient::find($ExtraIngredientData['id']);
                    $INGQTY = $ExtraIngredient->quantity * $PROQTY;
                }
                foreach($product['ingredient'] as $ingredientData) {
                    $ingredient = Repo::find($ingredientData['id']);
                    $REPOQTY = $ingredient->qty;
                }
                $newQTY = $REPOQTY - $INGQTY;
                $ingredient->update(['qty' => $newQTY]);   
            }
            event(new ToCasher($order));
            event(new ToWaiter($order));
            return $this->apiResponse($order, 'Changes saved successfully', 201);
        }
    }
}
