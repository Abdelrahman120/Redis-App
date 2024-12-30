<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $products = [];
        for ($i = 0; $i < 500; $i++) { 
            $products[] = [
                'name' => 'Product ' . $i,
                'price' => rand(10, 500), 
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Product::insert($products);
    }
}
