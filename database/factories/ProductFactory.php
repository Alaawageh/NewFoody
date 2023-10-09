<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    
    public function definition()
    {
        $names = ['burger','crispy','pizza','cheesy broccoli soup','sushi','shrimp Alfredo','garlic butter steak','Greek Beef Pitas'];
        // DB::table('products')->truncate();
        return [
            'name' => $this->faker->randomElement($names),
            'description' => $this->faker->text(),
            'price' => $this->faker->numerify(),
            'position' => $this->faker->numerify(),
            'estimated_time'=>$this->faker->dateTime(),
            'status'=>$this->faker->boolean(),
            'branch_id'=> 1,
            'category_id' => \App\Models\Category::all()->random()->id,
            
        ];



    }
}
