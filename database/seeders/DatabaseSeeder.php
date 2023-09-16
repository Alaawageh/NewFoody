<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\Category;
use App\Models\ExtraIngredient;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
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
        

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
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
        Table::create([
            'table_num' => 1,
            'branch_id' => $branch->id
        ]);
        Branch::factory()->count(3)->create();
        Category::factory()->count(3)->create();
        // 
        Ingredient::factory(20)->create();
        ExtraIngredient::factory(20)->create();
        Product::factory(20)->create();
        Order::factory(20)->create();
        $this->call([
            UsersSeeder::class
        ]);
    }
}
