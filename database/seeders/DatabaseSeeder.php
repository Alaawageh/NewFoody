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
            'email'=> 'superadmin@gmail.com',
            'password' => bcrypt('123456789'),
            'user_type' => UserTypes::SUPER_ADMIN,
            'branch_id' => $branch->id
        ]);
        User::create([
            'email'=> 'admin@gmail.com',
            'password' => bcrypt('123456789'),
            'user_type' => UserTypes::ADMIN,
            'branch_id' => $branch->id
        ]);
        User::create([
            'email'=> 'kitchen@gmail.com',
            'password' => bcrypt('123456789'),
            'user_type' => UserTypes::KITCHEN,
            'branch_id' => $branch->id
        ]);
        User::create([
            'email'=> 'casher@gmail.com',
            'password' => bcrypt('123456789'),
            'user_type' => UserTypes::CASHER,
            'branch_id' => $branch->id
        ]);
        User::create([
            'email'=> 'waiter@gmail.com',
            'password' => bcrypt('123456789'),
            'user_type' => UserTypes::WAITER,
            'branch_id' => $branch->id
        ]);
  
    }
}
