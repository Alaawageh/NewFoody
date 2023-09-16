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
    public function mostRequestedProduct(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $mostRequestedProduct = OrderProduct::selectRaw('SUM(qty) as most_order , product_id');
        if ($year && $month && $day) {
            $mostRequestedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->whereDay('created_at', $day);
        } elseif ($year && $month) {
            $mostRequestedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } elseif ($year) {
            $mostRequestedProduct->whereYear('created_at', $year);
        } elseif ($day) {
            $mostRequestedProduct->whereDay('created_at', $day);
        }
        $order = $mostRequestedProduct->groupBy('product_id')
        ->orderByRaw('SUM(qty) DESC')
        ->limit(5)->get();
        
        return $this->apiResponse($order,'success',200);
    }
    public function leastRequestedProduct(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $leastRequestedProduct = OrderProduct::selectRaw('SUM(qty) as most_order , product_id')
        ->groupBy('product_id');

        if ($year && $month && $day) {
            $leastRequestedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->whereDay('created_at', $day);
        } elseif ($year && $month) {
            $leastRequestedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } elseif ($year) {
            $leastRequestedProduct->whereYear('created_at', $year);
        } elseif ($day) {
            $leastRequestedProduct->whereDay('created_at', $day);
        }
        $order = $leastRequestedProduct->orderByRaw('SUM(qty) ASC')->limit(5)->get();
        
        return $this->apiResponse($order,'success',200);
    }
    public function mostRatedProduct(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $mostRatedProduct = Rating::selectRaw('SUM(value) as RateProduct , product_id');
        if ($year && $month && $day) {
            $mostRatedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->whereDay('created_at', $day);
        } elseif ($year && $month) {
            $mostRatedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } elseif ($year) {
            $mostRatedProduct->whereYear('created_at', $year);
        } elseif ($day) {
            $mostRatedProduct->whereDay('created_at', $day);
        }
        $order = $mostRatedProduct->groupBy('product_id')
        ->orderByRaw('SUM(value) DESC')
        ->limit(5)
        ->get();
        return $this->apiResponse($order,'The most rated product',200);
    }
    public function leastRatedProduct(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $leastRatedProduct = Rating::selectRaw('SUM(value) as RateProduct , product_id');
        if ($year && $month && $day) {
            $leastRatedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->whereDay('created_at', $day);
        } elseif ($year && $month) {
            $leastRatedProduct->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } elseif ($year) {
            $leastRatedProduct->whereYear('created_at', $year);
        } elseif ($day) {
            $leastRatedProduct->whereDay('created_at', $day);
        }
        $order = $leastRatedProduct->groupBy('product_id')
        ->orderByRaw('SUM(value) ASC')
        ->limit(5)
        ->get();
       
        return $this->apiResponse($order,'The least rated product',200);
    }

    public function peakTimes(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $peakHours = Order::selectRaw('HOUR(time) as Hour')->groupByRaw('HOUR(time)')->orderBYRaw('COUNT(HOUR(time)) DESC');
        if ($year && $month && $day) {
            $peakHours->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->whereDay('created_at', $day);
        } elseif ($year && $month) {
            $peakHours->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } elseif ($year) {
            $peakHours->whereYear('created_at', $year);
        } elseif ($day) {
            $peakHours->whereDay('created_at', $day);
        }
        $order = $peakHours->get();
        return $this->apiResponse($order,'This time is peak time',200);
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

   public function avgRatingOrder(Request $request)
   {
    $year = $request->year;
    $month = $request->month;
    $day = $request->day;

    $avgOrder = Order::selectRaw('round(AVG(serviceRate),2) as average_serviceRate');
    if ($year && $month && $day) {
    $avgOrder->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereDay('created_at', $day);
    } elseif ($year && $month) {
        $avgOrder->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);
    } elseif ($year) {
        $avgOrder->whereYear('created_at', $year);
    } elseif ($day) {
        $avgOrder->whereDay('created_at', $day);
    }
    $order = $avgOrder->get();
       return $this->apiResponse($order,'average Rating for service',200);
   }



}
