<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;

class OrderProductTableSeeder extends Seeder
{
    public function run()
    {
        ini_set('memory_limit', '512M'); 

        Order::chunk(1000, function ($orders) {
            $products = Product::pluck('id')->toArray();

            $orderProductData = [];
            foreach ($orders as $order) {
                $randomProducts = array_rand($products, rand(1, 5));
                foreach ((array) $randomProducts as $productIndex) {
                    $orderProductData[] = [
                        'order_id' => $order->id,
                        'product_id' => $products[$productIndex],
                        'quantity' => rand(1, 10),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($orderProductData) >= 1000) {
                        \DB::table('order_product')->insert($orderProductData);
                        $orderProductData = [];
                    }
                }
            }

            if (!empty($orderProductData)) {
                \DB::table('order_product')->insert($orderProductData);
            }
        });
    }
}
