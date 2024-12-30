<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Console\Command;

class CacheOrdersData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orderCount = Order::count();
        
        $progress = new ProgressBar(app('console')->getOutput(), $orderCount);
        $progress->start();

        Order::with(['customer', 'products'])
            ->chunk(100, function ($orders) use ($progress) {
                $ordersData = $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->customer->name ?? 'N/A',
                        'products' => $order->products->pluck('name')->join(', '),
                        'total_quantity' => $order->products->sum('pivot.quantity'),
                        'created_at' => $order->created_at
                    ];
                });

                Cache::put('orders_data_cache', $ordersData, now()->addMinutes(60));
                
                $progress->advance(100);
            });


        $progress->finish();
    }
}
