<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $names = ['meat','cheese','tomatoes','Potato','chicken','lettuce','onion','garlic'];
        // DB::table('ingredients')->truncate();
        return [
            'name' => $this->faker->randomElement($names),
            'total_quantity' => $this->faker->numerify(),
            'branch_id'=> 1,
        ];
    }
}
