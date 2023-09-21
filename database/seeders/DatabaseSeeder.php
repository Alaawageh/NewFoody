<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\Category;
use App\Models\ExtraIngredient;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductExtraIngredient;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\ProductIngredient;
use App\Models\Rating;
use App\Models\Repo;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use App\Types\UserTypes;
use Database\Factories\ExtraFactory;
use Database\Factories\ProductExtraIngredientFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $Restaurant = Restaurant::create([
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('123456789')
            ]);
            $branch = Branch::create([
                'name'=> 'one',
                'address' => 'one',
                'taxRate' => '15%',
                'restaurant_id' => $Restaurant->id
           
            ]);
        
        User::create([
            'email'=> 'admin@gmail.com',
            'password' => bcrypt('123456789'),
            'user_type' => UserTypes::SUPER_ADMIN,
            'branch_id' => $branch->id
        ]);
        // Restaurant::factory()->count(3)->create();
        // Branch::factory()->count(3)->create();
        // User::factory()->count(10)->create();
        // Ingredient::factory(30)->create();
        // ExtraIngredient::factory(30)->create();
        // Category::factory()->count(7)->create();
        // Table::factory()->count(10)->create();
        // Product::factory(30)->create();
        // ProductIngredient::factory(30)->create();
        // ProductExtraIngredient::factory(30)->create();
        // Order::factory(80)->create();
        // OrderProduct::factory(70)->create();
        // Rating::factory(40)->create();
        // OrderProductExtraIngredient::factory(40)->create();

  
    }
}
