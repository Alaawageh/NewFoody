<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\Category;
use App\Models\ExtraIngredient;
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
        // Restaurant::create([
        //     "name" => "one",
        //     'user_id' => '1',
        // ]);
    }
}
