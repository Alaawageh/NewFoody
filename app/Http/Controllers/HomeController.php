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

    public function countOrder(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;

        $query =  Order::selectRaw('COUNT(*) as countOrder');
        if($year && $month && $day) {
            $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereDay('created_at', $day);
        }
        $order = $query->get();
        
        return $this->apiResponse($order,'The number of orders by day',200);
    }
    
    public function TotalSalesByMonth(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $order = Order::selectRaw('SUM(total_price) as totalSales')->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        return $this->apiResponse($order,'success',200);
    }
    public function maxSales(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $maxSales= Order::selectRaw('MAX(total_price) as Max_Sales')->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        return $this->apiResponse($maxSales,'success',200);

    }
    public function avgSalesByYear(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $avgSalesByYear = Order::selectRaw('round(AVG(total_price),2) as Average_Sales')->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        
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

    public function peakTimes(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $peakHours = Order::selectRaw('HOUR(time) as Hour')->groupByRaw('HOUR(time)')->orderBYRaw('COUNT(HOUR(time)) DESC')->limit(5)->get();
        return $this->apiResponse($peakHours,'This time is peak time',200);
    }
    public function statistics(Request $request) {
            $year = $request->year;
            $month = $request->month;
            $day = $request->day;
            $query = Order::selectRaw('SUM(total_price) as total_sales, AVG(total_price) as avg_sales, MAX(total_price) as max_sales, COUNT(id) as total_orders, ROUND(AVG(id), 2) as avg_orders');
            if ($year && $month && $day) {
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month)
                      ->whereDay('created_at', $day);
            } elseif ($year && $month) {
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            } elseif ($year) {
                $query->whereYear('created_at', $year);
            } elseif ($day) {
                $query->whereDay('created_at', $day);
            }
            $order = $query->get();

            return $this->apiResponse($order, 'success', 200);

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
   public function countTables()
   {
        // $orders = Order::selectRaw('COUNT(author) as count')->groupBy('author')->get();
        $orders = Order::where('author',auth()->user()->email)->get();
        return $orders;
        // if ($orders->author == auth()->user()->email){
        //     return 1;
        // }
    
   }

}
