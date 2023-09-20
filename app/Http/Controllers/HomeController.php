<?php

namespace App\Http\Controllers;

use App\Http\Resources\HomeResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\RateProductResource;
use App\Http\Resources\RateServiceResource;
use App\Http\Resources\RatingResource;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponseTrait;

    public function countOrder(Request $request,Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;

        $query = Order::where('branch_id',$branch->id)->selectRaw('COUNT(*) as countOrder');
        if($year && $month && $day) {
            $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereDay('created_at', $day);
        }
        $order = $query->get();
        
        return $this->apiResponse($order,'The number of orders by day',200);
    }
    
    public function TotalSalesByMonth(Branch $branch)
    {
        $order = Order::where('branch_id',$branch->id)
        ->selectRaw('SUM(total_price) as totalSales, YEAR(created_at) as year , MONTH(created_at) as month, DAY(created_at) as day')
        ->groupBy('year','month','day')
        ->orderByRaw('year,month,day')
        ->get();
        return $this->apiResponse($order,'success',200);
    }
    public function maxSales(Branch $branch)
    {
        $maxSales= Order::where('branch_id',$branch->id)
        ->selectRaw('MAX(total_price) as Max_Sales,YEAR(created_at) as year , MONTH(created_at) as month, DAY(created_at) as day')
        ->groupBy('year','month','day')
        ->orderByRaw('year,month,day')
        ->get();
        return $this->apiResponse($maxSales,'success',200);

    }
    public function avgSalesByYear(Branch $branch)
    {
        $avgSalesByYear = Order::where('branch_id',$branch->id)
        ->selectRaw('round(AVG(total_price),2) as Average_Sales,YEAR(created_at) as year')
        ->groupBy('year')
        ->orderByRaw('year')
        ->get();
        
        return $this->apiResponse($avgSalesByYear,'success',200);
    }
    public function mostRequestedProduct(Request $request,Branch $branch)
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
         
        $mostRequestedProduct->whereHas('product.branch', function ($query) use ($branch) {
            $query->where('id', $branch->id);
        });
        $order = $mostRequestedProduct->groupBy('product_id')
            ->orderByRaw('SUM(qty) DESC')
            ->limit(5)
            ->get();

        
        return $this->apiResponse(HomeResource::collection($order),'success',200);
    }
    public function leastRequestedProduct(Request $request,Branch $branch)
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
        $leastRequestedProduct->whereHas('product.branch', function ($query) use ($branch) {
            $query->where('id', $branch->id);
        });
        $order = $leastRequestedProduct->orderByRaw('SUM(qty) ASC')->limit(5)->get();
        
        return $this->apiResponse(HomeResource::collection($order),'success',200);
    }
    public function mostRatedProduct(Request $request,Branch $branch)
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
        $mostRatedProduct->whereHas('product.branch', function ($query) use ($branch) {
            $query->where('id', $branch->id);
        });
        $order = $mostRatedProduct->groupBy('product_id')
        ->orderByRaw('SUM(value) DESC')
        ->limit(5)
        ->get();
        return $this->apiResponse(RateProductResource::collection($order),'The most rated product',200);
    }
    public function leastRatedProduct(Request $request,Branch $branch)
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
        $leastRatedProduct->whereHas('product.branch', function ($query) use ($branch) {
            $query->where('id', $branch->id);
        });
        $order = $leastRatedProduct->groupBy('product_id')
        ->orderByRaw('SUM(value) ASC')
        // ->limit(5)
        ->get();
       
        return $this->apiResponse(RateProductResource::collection($order),'The least rated product',200);
    }

    public function peakTimes(Request $request,Branch $branch)
    {
        $date = $request->date;
        $peakHours = Order::where('branch_id',$branch->id)->selectRaw('FLOOR(HOUR(created_at) / 2) * 2 as RangeHour , COUNT(*) as order_count')
                ->whereDate('created_at', $date)
                ->groupByRaw('FLOOR(HOUR(created_at) / 2) * 2')
                ->orderBYRaw('COUNT(HOUR(created_at)) DESC')
                ->get();
        return $this->apiResponse($peakHours,'This time is peak time',200);
    }
    public function statistics(Request $request,Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $query = Order::where('branch_id',$branch->id)->selectRaw('SUM(total_price) as total_sales, AVG(total_price) as avg_sales, MAX(total_price) as max_sales, COUNT(id) as total_orders, ROUND(AVG(id), 2) as avg_orders');
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


    public function readyOrder(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('time_end','!=',null)->get();
        $count = 0;
        $totalDifference = 0;
        foreach ($orders as $order) {
            $start = Carbon::parse($order->time);
            $end = Carbon::parse($order->time_end);
            $preparationTime = $start->diffInSeconds($end);
            $totalDifference += $preparationTime;
            // return $totalDifference;
            $count++;
        }

        if ($count > 0) {
            $avgPreparationTime = $preparationTime / $count;
            
            return $this->apiResponse(round($avgPreparationTime/3600 , 2), 'average preparation time', 200);
        } else {
            return $this->apiResponse(null, 'Not found', 200);
        }
    }
   public function timefromDone(Branch $branch)
   {
        $orders = Order::where('branch_id',$branch->id)->get();
        $count = 0;
        foreach ($orders as $order) {
            $start = Carbon::parse($order->time_end);
            $end = Carbon::parse($order->time_Waiter);
            $diff = $start->diffInSeconds($end);
            
            $count++;
        }
        if ($count > 0) {
            $avgPreparationTime = $diff / $count;

            return $this->apiResponse(round($avgPreparationTime/3600 , 2), 'average Time between from kitchen to waiter', 200);
        } else {
            return $this->apiResponse(null, 'no orders found', 200);
        }
   }
   public function timeReady(Branch $branch)
   {
    $orders = Order::where('branch_id',$branch->id)->get();
        $count = 0;
        foreach ($orders as $order) {
            $start = Carbon::parse($order->time);
            $end = Carbon::parse($order->time_Waiter);
            $fromtoclient = $start->diffInSeconds($end);
            $count++;
        }
        if ($count > 0) {
            $avgPreparationTime = $fromtoclient / $count;

            return $this->apiResponse(round($avgPreparationTime/3600 , 2), 'average Time between from client Request to receive', 200);
        } else {
            return $this->apiResponse(null, 'no orders found', 200);
        }

   }

//    public function avgRatingProduct(Product $product)
//    {
//        $avgRate = $product->rating->avg('value');
//        return $this->apiResponse(round($avgRate,2),'average Rating for each product',200);
//    }
    public function avgRatingProduct(Request $request,Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day; 
        $product = Product::where('branch_id',$branch->id);
        if ($year && $month && $day) {
            $product->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
            } elseif ($year && $month) {
                $product->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month);
            } elseif ($year) {
                $product->whereYear('created_at', $year);
            } elseif ($day) {
                $product->whereDay('created_at', $day);
            }
            $products = $product->get();

            return $this->apiResponse(ProductResource::collection($products),'average Rating for each product',200);

            // return $products;
            // foreach($products as $one) {
            //     $productRate = $one->rating->avg('value');
            //     return $this->apiResponse(round($productRate,2),'average Rating for each product',200);
            // }

    }

   public function avgRatingOrder(Request $request,Branch $branch)
   {
    $year = $request->year;
    $month = $request->month;
    $day = $request->day;

    $avgOrder = Order::where('branch_id',$branch->id)->selectRaw('round(AVG(serviceRate),2) as average_serviceRate');
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
   public function getfeedbacks(Branch $branch)
   {
    $orders = Order::where('branch_id',$branch->id)->where('serviceRate','!=',null)->where('feedback','!=',null)->get();
    // foreach($orders as $order) {
        
    // }
    return $this->apiResponse(RateServiceResource::collection($orders),'success',200);

   }


}
