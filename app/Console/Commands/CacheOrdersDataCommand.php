<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class CacheOrdersDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:orders-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all orders data for faster retrieval using chunking.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');// Set memory limit to unlimited

        $this->info('Starting to cache orders data...');// Display a message to indicate the process is starting

        Cache::flush();// Clear existing cache before caching

        $cacheKeyPrefix = 'orders_data_cache_chunk_';
        $chunkIndex = 1;
        $maxRecords = 10000; 
        $cachedRecords = 0;
        Order::with(['customer', 'products'])
            ->select('orders.*')
            ->chunk(1000, function ($orders) use ($cacheKeyPrefix, &$chunkIndex, &$cachedRecords, $maxRecords) {
                if ($cachedRecords >= $maxRecords) {
                    $this->info("Reached max records limit: $maxRecords. Stopping further caching.");
                    return false;
                }
                $ordersData = $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->customer->name ?? 'N/A',
                        'products' => $order->products->pluck('name')->join(', '),
                        'total_quantity' => $order->products->sum('pivot.quantity'),
                        'created_at' => $order->created_at->toDateTimeString(),
                    ];// Map the order data to the desired format
                });
                Cache::put($cacheKeyPrefix . $chunkIndex, $ordersData, now()->addMinutes(60));// Cache the orders data with a unique key & for 60 minutes
                $cachedRecords += $ordersData->count();
                $this->info("Chunk $chunkIndex cached with " . $ordersData->count() . " records. Total cached: $cachedRecords");// Display a message with the number of cached records
                $chunkIndex++;
            });
        Cache::put('orders_data_cache_chunks', $chunkIndex - 1, now()->addMinutes(60));// Cache the total number of chunks

        $this->info('All orders data has been cached successfully!');
    }
}
