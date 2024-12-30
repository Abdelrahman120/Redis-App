<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Customer;

class FillCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fillCustomer:Cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fill Cache with data from database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = 0; 
    
        Customer::select('id', 'national_id', 'name', 'email')->chunk(100, function ($customers) use (&$count) {
            foreach ($customers as $customer) {
                if ($count >= 1000) {
                    return false; 
                }
    
                $key = 'customer_' . $customer->id;
                Cache::put($key, [
                    'id' => $customer->id,
                    'national_id' => $customer->national_id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                ], 3600); 
    
                $count++;
            }
        });
    
        $this->info("First 1000 records cached successfully!");
    }
    
}
