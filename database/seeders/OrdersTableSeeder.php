<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Customer;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        ini_set('memory_limit', '-1');

        $customers = Customer::all();

        $orders = [];
        foreach ($customers as $customer) {
            for ($i = 0; $i < 100; $i++) {
                $orders[] = [
                    'customer_id' => $customer->id,
                    'order_date' => now()->subDays(rand(0, 365)), 
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($orders) >= 1000) {
                    Order::insert($orders);
                    $orders = [];
                }
            }
        }

        if (!empty($orders)) {
            Order::insert($orders);
        }
    }
}
