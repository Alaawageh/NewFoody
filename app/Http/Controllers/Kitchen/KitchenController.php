<?php

namespace App\Http\Controllers\Kitchen;

use App\Events\IngredientMin;
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
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\ProductIngredient;
use App\Models\Table;
use Carbon\Carbon;

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

    public function ChangeToDone(Order $order)
    {
        if ($order->status = '2') {
            $order->update([
                'status' => '3',
                'time_end' => now(),
            ]);
            $order->save(); 
            $lowIngredients = [];
            $branch = $order->branch;
            foreach ($order->products as $productData) {
                foreach($productData['extra'] as $one) {
                    $extraIngredients = ProductExtraIngredient::where('product_id',$productData['product_id'])->where('extra_ingredient_id',$one->id)->first();
                    if ($extraIngredients->unit == "g" && $one->ingredient->unit == "kg" || $extraIngredients->unit == "ml" && $one->ingredient->unit == "l"){
                        $totalQuantityNeeded = ($extraIngredients->quantity / 1000) * $productData->qty;
                        $one->ingredient->total_quantity -= $totalQuantityNeeded;
                        $one->ingredient->save();
                    }elseif($extraIngredients->unit == "kg" && $one->ingredient->unit == "g" || $extraIngredients->unit == "l" && $one->ingredient->unit == "ml") {
                        $totalQuantityNeeded = ($extraIngredients->quantity * 1000) * $productData->qty;
                        $one->ingredient->total_quantity -= $totalQuantityNeeded;
                        $one->ingredient->save();
                    }
                    else{
                        $one->ingredient->total_quantity -= $extraIngredients->quantity * $productData->qty;
                        $one->ingredient->save();
                    }
                }
                $productIngredients = $productData->product->ingredients;
                foreach($productIngredients as $ingredient) {
                    $isRemoved = 1;
                    foreach ($productData['ingredients'] as $removed) {
                        if ($removed['id'] == $ingredient->id) {
                            $isRemoved = 0;
                            break;
                        }
                    }
                    if ($isRemoved) {
                        if ($ingredient->unit == "kg" && $ingredient->pivot->unit == "g" || $ingredient->unit == "l" && $ingredient->pivot->unit == "ml") {
                            $totalQuantityNeeded = ($ingredient->pivot->quantity / 1000) * $productData->qty;
                            $ingredient->total_quantity -= $totalQuantityNeeded;
                            $ingredient->save();
                        }elseif ($ingredient->unit == "g" && $ingredient->pivot->unit == "kg" || $ingredient->unit == "ml" && $ingredient->pivot->unit == "l") {
                            $totalQuantityNeeded = ($ingredient->pivot->quantity * 1000) * $productData->qty;
                            $ingredient->total_quantity -= $totalQuantityNeeded;
                            $ingredient->save();
                        }else{
                            $ingredient->total_quantity -= $ingredient->pivot->quantity * $productData->qty;
                            $ingredient->save();  
                        }
                        if ($ingredient->total_quantity < 0) {
                            $ingredient->total_quantity = max(0,$ingredient->total_quantity);
                            $ingredient->save();
                        }
                    }

                }
                if($ingredient->total_quantity <= $ingredient->threshold) {
                    $ingredientData = [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                        'total_quantity' => $ingredient->total_quantity,
                        'threshold' =>$ingredient->threshold,
                        'branch' => $ingredient->branch
                    ];
                    $lowIngredients[] = $ingredientData;
                }
                if(isset($ingredient) && $ingredient->total_quantity <= $ingredient->threshold) {
                    $lowIngredients = array_unique($lowIngredients, SORT_REGULAR);
                    event(new IngredientMin($lowIngredients,$branch));
                }
                if($order->takeaway == false) {
                    event(new ToWaiter($order));
                }
                $branch = $order->branch;
                $bill = Bill::where('id',$order->bill_id)->where('is_paid',0)->latest()->first();
                if($bill) {
                    event(new ToCasher($bill,$branch));
                }
            
                return $this->apiResponse(OrderResource::make($order), 'Changes saved successfully', 201);
    
            }
        }

    }
    // public function ChangeToDone(Order $order)
    // {
    //     if ($order->status == '2') {
    //         $this->updateOrderStatus($order);
    //         $this->updateIngredients($order);
    //         $this->checkIngredientThreshold($order);
    //         $this->triggerEvents($order);
    //         return $this->apiResponse(OrderResource::make($order), 'Changes saved successfully', 201);
    //     }
    // }
    // private function updateOrderStatus(Order $order)
    // {
    //     $order->update([
    //         'status' => '3',
    //         'time_end' => now(),
    //     ]);
    //     $order->save();
    // }
    // private function updateIngredients(Order $order)
    // {
    //     foreach ($order->products as $productData) {
    //         $this->updateExtraIngredients($productData);
    //         $this->updateProductIngredients($productData);
    //     }
    // }

    // private function updateExtraIngredients($productData)
    // {
    //     foreach($productData['extra'] as $one) {
    //         $extraIngredients = ProductExtraIngredient::where('product_id',$productData['product_id'])->where('extra_ingredient_id',$one->id)->first();
    //         $this->updateIngredientQuantity($extraIngredients, $one, $productData->qty);
    //     }
    // }
    // private function updateProductIngredients($productData)
    // {
    //     $productIngredients = $productData->product->ingredients;
    //     foreach($productIngredients as $ingredient) {
    //         $isRemoved = 1;
    //         foreach ($productData['ingredients'] as $removed) {
    //             if ($removed['id'] == $ingredient->id) {
    //                 $isRemoved = 0;
    //                 break;
    //             }
    //         }
    //         if ($isRemoved) {
    //             $this->updateIngredientQuantity($ingredient, $ingredient->pivot, $productData->qty);
    //         }
    //     }
    // }
    // private function updateIngredientQuantity($ingredient, $ingredientPivot, $productQty)
    // {
    //     $totalQuantityNeeded = $this->calculateTotalQuantityNeeded($ingredient, $ingredientPivot, $productQty);
    //     $ingredient->total_quantity -= $totalQuantityNeeded;
    //     $ingredient->save();
    //     if ($ingredient->total_quantity < 0) {
    //         $ingredient->total_quantity = max(0,$ingredient->total_quantity);
    //         $ingredient->save();
    //     }
    // }
    // private function calculateTotalQuantityNeeded($ingredient, $ingredientPivot, $productQty)
    // {
    //     if ($ingredient->unit == $ingredientPivot->unit) {
    //         return $ingredientPivot->quantity * $productQty;
    //     } elseif ($ingredient->unit == "kg" && $ingredientPivot->unit == "g" || $ingredient->unit == "l" && $ingredientPivot->unit == "ml") {
    //         return ($ingredientPivot->quantity / 1000) * $productQty;
    //     } elseif ($ingredient->unit == "g" && $ingredientPivot->unit == "kg" || $ingredient->unit == "ml" && $ingredientPivot->unit == "l") {
    //         return ($ingredientPivot->quantity * 1000) * $productQty;
    //     } else {
    //         return $ingredientPivot->quantity;
    //     }
    // }
    // private function checkIngredientThreshold(Order $order)
    // {
    //     $lowIngredients = [];
    //     foreach ($order->products as $productData) {
    //         $productIngredients = $productData->product->ingredients;
    //         foreach($productIngredients as $ingredient) {
    //             if($ingredient->total_quantity <= $ingredient->threshold) {
    //                 $ingredientData = [
    //                     'id' => $ingredient->id,
    //                     'name' => $ingredient->name,
    //                     'total_quantity' => $ingredient->total_quantity,
    //                     'threshold' =>$ingredient->threshold,
    //                     'branch' => $ingredient->branch
    //                 ];
    //                 $lowIngredients[] = $ingredientData;
    //             }
    //         }
    //     }
    //     $branch = $order->branch;
    //     if(isset($ingredient) && $ingredient->total_quantity <= $ingredient->threshold) {
    //         $lowIngredients = array_unique($lowIngredients, SORT_REGULAR);
    //         event(new IngredientMin($lowIngredients,$branch));
    //     }
    //     return $lowIngredients;
    // }
    // private function triggerEvents(Order $order)
    // {
    //     $branch = $order->branch;
    //     $bill = Bill::where('id',$order->bill_id)->where('is_paid',0)->latest()->first();
    //     if($bill) {
    //         event(new ToCasher($bill,$branch));
    //     }
    //     if($order->takeaway == false) {
    //         event(new ToWaiter($order));
    //     }
    // }

    public function delete(Order $order)
    {
        $order->delete();
        return $this->apiResponse(null,'Deleted Successfully',200);

    }
}
