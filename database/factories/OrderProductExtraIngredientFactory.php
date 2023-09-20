<?php

namespace Database\Factories;

use App\Models\ExtraIngredient;
use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProductExtraIngredient>
 */
class OrderProductExtraIngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_product_id' => OrderProduct::all()->random()->id,
            'extra_ingredient_id' => ExtraIngredient::all()->random()->id
        ];
    }
}
