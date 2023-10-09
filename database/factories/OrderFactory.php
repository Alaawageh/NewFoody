<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    
    public function definition()
    {
        // DB::table('orders')->truncate();
        $status = ['1','2','3'];
        $serviceRate =  ['1','2','3','4','5'];
        $bill = ['1','2','3','4','5'];
        $start_date = $this->faker->dateTimeBetween('2020-01-01');
        return [
            'status'=>$this->faker->randomElement($status),
            'table_id'=>Table::all()->random()->id,
            'is_paid'=>$this->faker->boolean(),
            'is_update'=>$this->faker->boolean(),
            'total_price' =>$this->faker->numerify(),
            'time'=>$this->faker->dateTime(),
            'time_start'=>$this->faker->dateTime(),
            'time_end'=>$this->faker->dateTime(),
            'time_Waiter'=>$this->faker->dateTime(),
            'serviceRate' => $this->faker->randomElement($serviceRate),
            'feedback' => $this->faker->sentence(),
            'branch_id'=> 1,
            'bill_id' => $this->faker->randomElement($bill),
            'created_at'=> $this->faker->dateTimeBetween($start_date , now())
        ];
    }
}
