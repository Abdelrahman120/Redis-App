<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer;
use App\Helpers\sendMail;

class SendSubscriptionExpireMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $customer;
    private $expire_date;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer, $expire_date)
    {
        $this->customer = $customer;
        $this->expire_date = $expire_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('i am now in line 34 in job class');
        // to do send email for each expired customer
        sendMail('emails.subscription_expiration', $this->customer->email, 'Subscription Expire', ['customer' => $this->customer, 'expire_date' => $this->expire_date]);
    }
}
