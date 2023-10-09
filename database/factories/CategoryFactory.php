<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $names = ['Meals','Sandwiches','Soups','Salads','Desserts'];
        return [
            'name' => $this->faker->randomElement($names),
            'status' => $this->faker->boolean(),
            'branch_id'=> 1,
        ];
    }
}
