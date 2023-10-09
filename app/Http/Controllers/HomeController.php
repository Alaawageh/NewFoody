<?php

namespace App\Http\Controllers;

use App\Http\Resources\HomeResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\RateProductResource;
use App\Http\Resources\RateServiceResource;
use App\Http\Resources\RatingResource;
use App\Http\Resources\WaiterResource;
use App\Http\Resources\WaitResource;
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
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query = Order::where('branch_id',$branch->id);
        if($year && $month && $day) {
            $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereDay('created_at', $day)
            ->selectRaw('COUNT(*) as countOrder');
        } elseif ($year && $month) {
            $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->selectRaw('COUNT(*) as countOrder');
        } elseif ($year) {
            $query->whereYear('created_at', $year)
                ->selectRaw('COUNT(*) as countOrder');
        
        } elseif ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])->selectRaw('COUNT(*) as countOrder');
        } elseif ($startDate) {
            $query->whereDate('created_at', $startDate)->selectRaw('COUNT(*) as countOrder');
        } elseif ($endDate) {
            $query->whereDate('created_at', $endDate)->selectRaw('COUNT(*) as countOrder');
        }
        $order = $query->get();
        
        return $this->apiResponse($order,'The number of orders by day',200);
    }

    public function TotalSalesByMonth(Request $request,Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        if($year && $month) {
            $query = Order::where('branch_id', $branch->id)->selectRaw('ROUND(SUM(total_price)) as totalSales, DAY(created_at) as day')
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)->groupBy('day')->orderByRaw('day')
            ->get();
            return $this->apiResponse($query,'success',200);

        }elseif($year) {
            $query = Order::where('branch_id', $branch->id)->selectRaw('SUM(total_price) as totalSales, MONTH(created_at) as month')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderByRaw('month')
            ->get();
            return $this->apiResponse($query,'success',200);
        }

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
        $startDate = $request->start_date;
        $endDate = $request->end_date;
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
        } elseif ($startDate && $endDate) {
            $mostRequestedProduct->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $mostRequestedProduct->whereDate('created_at', $startDate);
        } elseif ($endDate) {
            $mostRequestedProduct->whereDate('created_at', $endDate);
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
        $startDate = $request->start_date;
        $endDate = $request->end_date;
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
        } elseif ($startDate && $endDate) {
            $leastRequestedProduct->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $leastRequestedProduct->whereDate('created_at', $startDate);
        } elseif ($endDate) {
            $leastRequestedProduct->whereDate('created_at', $endDate);
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
        $startDate = $request->start_date;
        $endDate = $request->end_date;
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
        } elseif ($startDate && $endDate) {
            $mostRatedProduct->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $mostRatedProduct->whereDate('created_at', $startDate);
        } elseif ($endDate) {
            $mostRatedProduct->whereDate('created_at', $endDate);
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
        $startDate = $request->start_date;
        $endDate = $request->end_date;
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
        } elseif ($startDate && $endDate) {
            $leastRatedProduct->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $leastRatedProduct->whereDate('created_at', $startDate);
        } elseif ($endDate) {
            $leastRatedProduct->whereDate('created_at', $endDate);
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

    public function peakTimes(Request $request, Branch $branch)
    {
        $date = $request->date;
        $endDate = $request->end_date;
        $peakHours = Order::where('branch_id', $branch->id)
            ->selectRaw('FLOOR(HOUR(created_at) / 2) * 2 as RangeHour, COUNT(*) as order_count');

        if ($date) {
            $peakHours->whereDate('created_at', $date);
        }

        if ($date && $endDate) {
            $peakHours->whereBetween('created_at', [$date, $endDate]);
        }
        
        $data = $peakHours->groupByRaw('FLOOR(HOUR(created_at) / 2) * 2')
            ->orderByRaw('COUNT(*) DESC')
            ->get();

        return $this->apiResponse($data, 'This time is peak time', 200);
    }
    public function statistics(Request $request,Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query = Order::where('branch_id',$branch->id)->selectRaw('ROUND(SUM(total_price)) as total_sales, ROUND(AVG(total_price),2) as avg_sales');
        if ($year && $month && $day) {
            $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day)
                    ->selectRaw('COUNT(*) as total_orders , ROUND(COUNT(*) / 1,2) AS avg_orders');
        } elseif ($year && $month) {
            $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->selectRaw('COUNT(*) as total_orders, ROUND(COUNT(*) / DAY(LAST_DAY(created_at)), 2) AS avg_orders');
        } elseif ($year) {
            $query->whereYear('created_at', $year)->selectRaw('COUNT(*) as total_orders , ROUND(COUNT(*) / 356,2 ) AS avg_orders');

        } elseif ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total_orders,ROUND(COUNT(*) / DATEDIFF(?, ?),2) as avg_orders', [$endDate, $startDate]);
        } elseif ($startDate) {
            $query->whereDate('created_at', $startDate)
            ->selectRaw('COUNT(*) as total_orders,ROUND(COUNT(*) / 1,2) AS avg_orders');
        } elseif ($endDate) {
            $query->whereDate('created_at', $endDate)
            ->selectRaw('COUNT(*) as total_orders,ROUND(COUNT(*) / 1,2) AS avg_orders');
        }
    
        $order = $query->orderBy('total_orders','desc')->first();

        return $this->apiResponse($order, 'success', 200);

    }

    public function readyOrder(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('time','!=',null)->where('time_end','!=',null)->get();
        $count = 0;
        foreach ($orders as $order) {
            $start = Carbon::parse($order->time);
            $end = Carbon::parse($order->time_end);
            $preparationTime = $start->diffInSeconds($end);

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
        $orders = Order::where('branch_id',$branch->id)->where('time_end','!=',null)->where('time_Waiter','!=',null)->get();
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
    $orders = Order::where('branch_id',$branch->id)->where('time','!=',null)->where('time_Waiter','!=',null)->get();
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

    public function avgRatingProduct(Request $request,Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day; 
        $startDate = $request->start_date;
        $endDate = $request->end_date;
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
            } elseif ($startDate && $endDate) {
                $product->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $product->whereDate('created_at', $startDate);
            } elseif ($endDate) {
                $product->whereDate('created_at', $endDate);
            }
            $products = $product->get();

            return $this->apiResponse(ProductResource::collection($products),'average Rating for each product',200);

    }

   public function avgRatingOrder(Request $request,Branch $branch)
   {
    $year = $request->year;
    $month = $request->month;
    $day = $request->day;
    $startDate = $request->start_date;
    $endDate = $request->end_date;
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
    } elseif ($startDate && $endDate) {
        $avgOrder->whereBetween('created_at', [$startDate, $endDate]);
    } elseif ($startDate) {
        $avgOrder->whereDate('created_at', $startDate);
    } elseif ($endDate) {
        $avgOrder->whereDate('created_at', $endDate);
    }
    $order = $avgOrder->get();
       return $this->apiResponse($order,'average Rating for service',200);
   }
   public function getfeedbacks(Branch $branch)
   {
    $orders = Order::where('branch_id',$branch->id)->where('serviceRate','!=',null)->where('feedback','!=',null)
    ->where('time','!=',null)->where('time_end','!=',null)->where('time_Waiter','!=',null)->get();

    return $this->apiResponse(RateServiceResource::collection($orders),'success',200);

   }

    public function countTables(Request $request , Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $waiters = Order::where('branch_id',$branch->id)->where('author','!=',null)->selectRaw('author AS waiter_name , COUNT(table_id) AS num_tables_served, round(AVG(time_waiter - time_end)/3600,2) AS avg_time_diff');
        if ($year && $month && $day) {
            $waiters->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
            } elseif ($year && $month) {
                $waiters->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month);
            } elseif ($year) {
                $waiters->whereYear('created_at', $year);
            } elseif ($day) {
                $waiters->whereDay('created_at', $day);
            } elseif ($startDate && $endDate) {
                $waiters->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $waiters->whereDate('created_at', $startDate);
            } elseif ($endDate) {
                $waiters->whereDate('created_at', $endDate);
            }
            $data = $waiters->groupBy('waiter_name')->get();
            
            if($data){
                return $this->apiResponse($data,'success',200);
            }else{
                return $this->apiResponse(null,'not found data in the specified dates',404);
            }
            
    }
    public function GetMax(Request $request , Branch $branch)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query = Order::where('branch_id',$branch->id);
            if ($year && $month && $day) {
                $query->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->whereDay('created_at', $day)
                        ->selectRaw('COUNT(*) as count, DATE(created_at) as date');
                } elseif ($year && $month) {
                    $query->whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->selectRaw('COUNT(*) as count, DATE(created_at) as date');
                } elseif ($year) {
                    $query->whereYear('created_at', $year)
                    ->selectRaw('COUNT(*) as count, DATE(created_at) as date');
                } elseif ($day) {
                    $query->whereDay('created_at', $day)
                    ->selectRaw('COUNT(*) as count, DATE(created_at) as date');
                } elseif ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('COUNT(*) as count, DATE(created_at) as date');
                } elseif ($startDate) {
                    $query->whereDate('created_at', $startDate)
                    ->selectRaw('COUNT(*) as count, DATE(created_at) as date');
                } elseif ($endDate) {
                    $query->whereDate('created_at', $endDate)
                    ->selectRaw('COUNT(*) as count, DATE(created_at) as date');
                }
                
            $data = $query->groupBy('date')->orderBy('count', 'desc')->first();
            if($data) {
                return $this->apiResponse($data,'success',200);
            }else{
                return $this->apiResponse(null,'No orders found in the specified dates',404);
            }
    }
    

}
