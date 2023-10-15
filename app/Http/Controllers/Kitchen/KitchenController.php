<?php

namespace App\Http\Controllers\Kitchen;

use App\Events\ToCasher;
use App\Events\ToWaiter;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\ExtraIngredient;
use App\Models\Ingredient;
use App\Models\Order;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;

class KitchenController extends Controller
{
    use ApiResponseTrait;

    public function getOrders(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('status',1)->where('is_paid',0)->get();
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
            return $this->apiResponse(OrderResource::make($order), 'Changes saved successfully', 200);

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

    // public function ChangeToDone(Order $order)
    // {
    //     if ($order->status = '2'){
    //         $order->update([
    //             'status' => '3',
    //             'time_end' => now(),
    //         ]);
    //         $order->save();
    //         $lowIngredients = [];
    //         $branch = $order->branch;
    //         // foreach ($order->products as $productData) {
    //         //     $product = Product::findOrFail($productData['product_id']);
    //         //     foreach ($productData['extra'] as $extra) {

    //         //         $extraingredient = ProductExtraIngredient::where('product_id',$product->id)->where('extra_ingredient_id',$extra->id)->first();
    //         //         $ingredient = Ingredient::findOrFail($extra['ingredient_id']);
    //         //         if($extraingredient->unit == "kg" || $extraingredient->unit == "l") {
    //         //             $QTY = ($extraingredient->quantity * 1000) * $productData['qty'];
    //         //             $ingredient->total_quantity -= $QTY;
    //         //             $ingredient->save();
    //         //             $ingredient->total_quantity = max(0,$ingredient->total_quantity);
    //         //             $ingredient->save();  
    //         //         }
    //         //         $QTY = $extraingredient->quantity * $productData['qty'];
    //         //         $ingredient->total_quantity -= $QTY;
    //         //         $ingredient->save();
    //         //         $ingredient->total_quantity = max(0,$ingredient->total_quantity);
    //         //         $ingredient->save();

    //         //     }
                
    //         //     foreach ($product->ingredients as $ingredient) {
    //         //         $isRemoved = 1;
    //         //         foreach ($productData['ingredients'] as $removed) {
    //         //             if ($removed['id'] == $ingredient->id) {
    //         //                 $isRemoved = 0;
    //         //                 break;
    //         //             }
    //         //         }
    //         //         if ($isRemoved) {
    //         //             if($ingredient->pivot->unit == "kg" || $ingredient->pivot->unit == "l") {
    //         //                 $quantity = ($ingredient->pivot->quantity * 1000) * $productData['qty'];
    //         //                 $ingredient->total_quantity -= $quantity;
    //         //                 $ingredient->save();
    //         //                 $ingredient->total_quantity = max(0,$ingredient->total_quantity);
    //         //                 $ingredient->save();
    //         //             }
    //         //             $quantity = $ingredient->pivot->quantity * $productData['qty'];
    //         //             $ingredient->total_quantity -= $quantity;
    //         //             $ingredient->save();
    //         //             $ingredient->total_quantity = max(0,$ingredient->total_quantity);
    //         //             $ingredient->save();
                        
    //         //             if($ingredient->total_quantity <= $ingredient->threshold) {
    //         //                 $ingredientData = [
    //         //                     'id' => $ingredient->id,
    //         //                     'name' => $ingredient->name,
    //         //                     'total_quantity' => $ingredient->total_quantity,
    //         //                     'threshold' =>$ingredient->threshold,
    //         //                     'branch' => $ingredient->branch
    //         //                 ];
    //         //                 $lowIngredients[] = $ingredientData;
    //         //             }
    //         //         }
    //         //     }

    //         // }
    //         // if(isset($ingredient) && $ingredient->total_quantity <= $ingredient->threshold) {
    //         //     $lowIngredients = array_unique($lowIngredients, SORT_REGULAR);
    //         //     event(new IngredientMin($lowIngredients,$branch));
    //         // }

    // foreach ($order->products as $orderProduct) {
    //     $productIngredients = $orderProduct->product->ingredients;
        
    //     foreach ($productIngredients as $ingredient) {
    //         $totalQuantityNeeded = $this->convertToBaseUnit($orderProduct->quantity, $orderProduct->unit) * $ingredient->pivot->qty;
    //         return $totalQuantityNeeded;
    //         // Deduct the total quantity needed for each ingredient from the current inventory quantity
    //         $ingredient->total_quantity -= $totalQuantityNeeded;

    //         // If any ingredient's inventory quantity becomes negative, raise an exception and roll back the transaction
    //         if ($ingredient->inventory_qty < 0) {
    //             throw new \Exception('Insufficient inventory for ' . $ingredient->name);
    //         }

    //         // Save the updated inventory quantity
    //         $ingredient->save();
    //     }
    // }
    //         if($order->takeaway == false) {
    //             event(new ToWaiter($order));
    //         }
            
           
    //         $branch = $order->branch;
    //         $bill = Bill::where('id',$order->bill_id)->where('is_paid',0)->latest()->first();
    //         if($bill) {
    //             event(new ToCasher($bill,$branch));
    //         }
            
            
            
    //         return $this->apiResponse(OrderResource::make($order), 'Changes saved successfully', 201);
    //     }
    // }

    public function delete(Order $order)
    {
        $order->delete();
        return $this->apiResponse(null,'Deleted Successfully',200);

    }

}
