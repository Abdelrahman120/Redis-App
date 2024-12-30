<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use Carbon\Carbon;
use App\Jobs\SendSubscriptionExpireMessageJob;

class SubscriptionExpiryNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:subscriptionExpiryNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check subscription expiry';

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
        $expired_customers = Customer::where('subscription_end_date', '<',now())->get();
        foreach ($expired_customers as $customer) {
            info('i am now in line 34 in command class');
            $expire_date = Carbon::createFromFormat('Y-m-d H:i:s',  $customer->subscription_end_date. ' 00:00:00')->toDateString();
            dispatch(new SendSubscriptionExpireMessageJob($customer, $expire_date));
        }
    }
}
