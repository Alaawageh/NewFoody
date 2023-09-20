<?php

namespace Database\Factories;

use App\Models\ExtraIngredient;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductExtraIngredient>
 */
class ProductExtraIngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::all()->random()->id,
            'extra_ingredient_id' => ExtraIngredient::all()->random()->id,
            'quantity' => $this->faker->numerify(),
            'price_per_piece' => $this->faker->numerify(),
        ];
    }
}
