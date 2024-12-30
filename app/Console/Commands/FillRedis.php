<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use Illuminate\Support\Facades\Redis;

class FillRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fillCustomer:redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fill redis with data from database';

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
        $customers = Customer::select('id', 'national_id')->get();
        if($customers && !empty($customers)){
            foreach ($customers as $customer) {
                Redis::set('national_'.$customer->id, $customer->national_id);
            }
        }
    }
}
