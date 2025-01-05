<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index');
    }
    public function database()
    {
        $data=Order::with(['customer', 'products'])
            ->select('orders.*');

        return datatables()->of($data)
            ->addColumn('customer_name', function ($order) {
                return $order->customer->name ?? 'N/A';
            })
            ->addColumn('products', function ($order) {
                return $order->products->pluck('name')->join(', ');
            })
            ->addColumn('total_quantity', function ($order) {
                return $order->products->sum('pivot.quantity');
            })
            ->addColumn('created_at', function ($order) {
                return $order->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['customer_name', 'products'])
            ->make(true);
            return view('orders.search', compact('data'))->with('i', function ($i) {
                return $i++;
            });
    }

    public function getAllData(Request $request)
    {
        return $this->fetchData($request);
    }

    private function fetchData(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $cacheKeyPrefix = 'orders_data_cache_chunk_';
        $chunkCount = Cache::get('orders_data_cache_chunks', 0);

        \Log::info("Chunk Count: {$chunkCount}");

        if ($chunkCount === 0) {
            \Log::warning("No cache chunks found for orders data.");
            return response()->json([
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ]);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchQuery = $request->input('searchQuery', null);

        \Log::info("Search Query: {$searchQuery}, Start: {$start}, Length: {$length}");

        $chunkData = Cache::get($cacheKeyPrefix . '1', collect());
        $recordsPerChunk = $chunkData->count();

        if ($recordsPerChunk === 0) {
            \Log::warning("No data found in first chunk.");
            return response()->json([
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ]);
        }

        $totalRecords = $recordsPerChunk * $chunkCount;
        $filteredRecords = 0;
        $filteredData = collect();

        if ($searchQuery) {
            for ($i = 1; $i <= $chunkCount; $i++) {
                $chunkData = Cache::get($cacheKeyPrefix . $i, collect());
                $filteredRecords += $chunkData->filter(function ($item) use ($searchQuery) {
                    return str_contains(strtolower($item['customer_name'] ?? ''), strtolower($searchQuery)) ||
                        str_contains(strtolower($item['products'] ?? ''), strtolower($searchQuery));
                })->count();
            }
        } else {
            $filteredRecords = $totalRecords;
        }

        $currentChunk = (int) floor($start / $recordsPerChunk) + 1;
        $chunkOffset = $start % $recordsPerChunk;

        while ($length > 0 && $currentChunk <= $chunkCount) {
            $chunkData = Cache::get($cacheKeyPrefix . $currentChunk, collect());

            if ($searchQuery) {
                $chunkData = $chunkData->filter(function ($item) use ($searchQuery) {
                    return str_contains(strtolower($item['customer_name'] ?? ''), strtolower($searchQuery)) ||
                        str_contains(strtolower($item['products'] ?? ''), strtolower($searchQuery));
                });
            }

            $chunkData = $chunkData->slice($chunkOffset, $length);

            $filteredData = $filteredData->concat($chunkData);
            $length -= $chunkData->count();
            $currentChunk++;
            $chunkOffset = 0;
        }

        \Log::info("Filtered Data Count: {$filteredData->count()}");

        return response()->json([
            'data' => $filteredData->values()->toArray(),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ]);
    }

    public function searchPage()
    {
        return view('orders.search'); // Make sure 'orders.search' is the correct view path
    }

    public function getSearchData(Request $request)
    {
        $searchQuery = $request->input('customerName', '');

        $cacheKeyPrefix = 'orders_data_cache_chunk_';
        $chunkCount = Cache::get('orders_data_cache_chunks', 0);

        $filteredOrders = collect();

        // Loop through cache chunks and filter orders
        for ($i = 1; $i <= $chunkCount; $i++) {
            $chunkData = Cache::get($cacheKeyPrefix . $i, collect());
            if ($chunkData->isEmpty()) continue;

            if ($searchQuery) {
                $chunkData = $chunkData->filter(function ($item) use ($searchQuery) {
                    return isset($item['customer_name']) && stripos($item['customer_name'], $searchQuery) !== false;
                });
            }

            $filteredOrders = $filteredOrders->concat($chunkData);
        }

        // Prepare data for response
        $data = $filteredOrders->map(function ($order) {
            return [
                'id' => $order['id'],
                'customer_name' => $order['customer_name'],
                'products' => $order['products'],
                'total_quantity' => $order['total_quantity'],
                'created_at' => $order['created_at'],
            ];
        });

        // Return all data (client-side pagination)
        return response()->json([
            'data' => $data->toArray(), // Send all records
        ]);
    }

    public function getCustomers(Request $request)
    {
        $searchQuery = $request->input('q', '');
        $cacheKeyPrefix = 'orders_data_cache_chunk_';
        $chunkCount = Cache::get('orders_data_cache_chunks', 0);

        $customers = collect();
        
        // Fetch customer names from cache
        for ($i = 1; $i <= $chunkCount; $i++) {
            $chunkData = Cache::get($cacheKeyPrefix . $i, collect());

            // Filter customers based on search query
            if ($searchQuery) {
                $chunkData = $chunkData->filter(function ($item) use ($searchQuery) {
                    return stripos($item['customer_name'], $searchQuery) !== false;
                });
            }

            $customers = $customers->concat($chunkData->pluck('customer_name')->unique());
        }

        return response()->json(
            $customers->map(function ($customer) {
                return ['id' => $customer, 'text' => $customer];
            })
        );
    } 
}
