<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $Rate =  ['1','2','3','4','5'];
        return [
            'product_id' => Product::all()->random()->id,
            'value' =>$this->faker->randomElement($Rate),
        ];
    }
}
