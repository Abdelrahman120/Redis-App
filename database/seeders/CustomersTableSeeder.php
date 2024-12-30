<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); 

        foreach (range(1, 100000) as $index) { 
            DB::table('customers')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'national_id' => (string)$faker->randomNumber(9, true),
                'subscription_end_date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
