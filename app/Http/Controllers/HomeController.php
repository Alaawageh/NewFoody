<?php

namespace App\Http\Controllers;

use App\Http\Resources\HomeResource;
use App\Http\Resources\RateProductResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponseTrait;

    public function countOrder()
    {
        $order = Order::selectRaw('DATE(created_at) as day, COUNT(*) as count')
        ->groupBy('DAY')
        ->get();
        return $this->apiResponse($order,'The number of orders by day',200);
    }
    
    public function TotalSalesByMonth()
    {
        $totalSales= Order::selectRaw('SUM(total_price) as total , MONTH(created_at) as month , YEAR(created_at) as year')
        ->groupBy('month','year')
        ->orderByRaw('year DESC , month ASC')
        ->get();
        return $this->apiResponse($totalSales,'success',200);
    }
    public function maxSales()
    {
        $maxSales= Order::selectRaw('MAX(total_price) as Max_Sales , MONTH(created_at) as month , YEAR(created_at) as year')
        ->groupBy('month','year')
        ->orderByRaw('year DESC , month ASC')
        ->get();
        return $this->apiResponse($maxSales,'success',200);

    }
    public function avgSalesByYear()
    {
        $avgSalesByYear = Order::selectRaw('round(AVG(total_price),2) as Average_Sales , YEAR(created_at) as year')
        ->groupBy('year')
        ->orderByRaw('year DESC')
        ->get();
        return $this->apiResponse($avgSalesByYear,'success',200);
    }
    public function mostRequestedProduct()
    {
        $mostRequestedProduct = OrderProduct::selectRaw('SUM(qty) as most_order , product_id')
        ->groupBy('product_id')
        ->orderByRaw('SUM(qty) DESC')
        ->limit(5)
        ->get();
        return $this->apiResponse(HomeResource::collection($mostRequestedProduct),'success',200);
    }
    public function leastRequestedProduct()
    {
        $leastRequestedProduct = OrderProduct::selectRaw('SUM(qty) as most_order , product_id')
        ->groupBy('product_id')
        ->orderByRaw('SUM(qty) ASC')
        ->limit(5)
        ->get();
        return $this->apiResponse(HomeResource::collection($leastRequestedProduct),'success',200);
    }
    public function mostRatedProduct()
    {
        $mostRatedProduct = Rating::selectRaw('SUM(value) as RateProduct , product_id')
        ->groupBy('product_id')
        ->orderByRaw('SUM(value) DESC')
        ->limit(5)
        ->get();
        return $this->apiResponse(RateProductResource::collection($mostRatedProduct),'The most rated product',200);
    }
    public function leastRatedProduct()
    {
        $leastRatedProduct = Rating::selectRaw('SUM(value) as RateProduct , product_id')
        ->groupBy('product_id')
        ->orderByRaw('SUM(value) ASC')
        ->limit(5)
        ->get();
        return $this->apiResponse(RateProductResource::collection($leastRatedProduct),'The least rated product',200);
    }

    public function peakTimes()
    {
        $peakHours = Order::selectRaw('HOUR(time) as Hour')->groupByRaw('HOUR(time)')->orderBYRaw('COUNT(HOUR(time)) DESC')->limit(5)->get();
        return $this->apiResponse($peakHours,'This time is peak time',200);
    }
    public function statistics(Request $request) {
        $start_at = $request->start_at;
        $end_at = $request->end_at;
        if($end_at && $start_at) {
            $order = Order::selectRaw('SUM(total_price) as total_sales , AVG(total_price) as avg_sales , MAX(total_price) as max_sales , COUNT(id) as total_orders , round(avg(id),2) as avg_orders')
            ->whereBetween('created_at',[$start_at,$end_at])
            ->get();
            return $this->apiResponse($order,'success',200);
        } elseif($start_at || $end_at) {
            $order = Order::selectRaw('SUM(total_price) as total_sales , AVG(total_price) as avg_sales , MAX(total_price) as max_sales , COUNT(id) as total_orders , round(avg(id),2) as avg_orders')
            ->where('created_at',$start_at)
            ->get();
            return $this->apiResponse($order,'success',200);
        }

    }
   public function readyOrder(Order $order)
   {
        $start = Carbon::parse($order->time);
        $end = Carbon::parse($order->time_end);
        $preparationTime = $start->diff($end)->format('%H:%I:%S');

        return $this->apiResponse($preparationTime,'time from client to kitchen',200);
   }
   public function timefromDone(Order $order)
   {
        $start = Carbon::parse($order->time_end);
        $end = Carbon::parse($order->time_Waiter);
        $diff = $start->diff($end)->format('%H:%I:%S');

        return $this->apiResponse($diff,'Time between from kitchen to waiter',200);
   }
   public function timeReady(Order $order)
   {
    $start = Carbon::parse($order->time);
    $end = Carbon::parse($order->time_Waiter);
    $fromtoclient = $start->diff($end)->format('%H:%I:%S');

    return $this->apiResponse($fromtoclient,'Time between from client Request to resive',200);
   }

   public function avgRatingProduct(Product $product)
   {
       $avgRate = $product->rating->avg('value');
       return $this->apiResponse(round($avgRate,2),'average Rating for each product',200);
   }

   public function avgRatingOrder()
   {
    
       $avgOrder = Order::selectRaw('round(AVG(serviceRate),2) as average_serviceRate')->get();
       return $this->apiResponse($avgOrder,'average Rating for service',200);
       
   }

}
