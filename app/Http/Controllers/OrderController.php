<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index'); 
    }

    public function searchPage()
    {
        return view('orders.search');
    }

    public function getAllData(Request $request)
    {
        return $this->fetchData($request);
    }

    public function getSearchData(Request $request)
    {
        $searchQuery = $request->input('searchQuery', null);

        if (!$searchQuery) {
            return response()->json([
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ]);
        }

        return $this->fetchData($request);
    }

    private function fetchData(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');  

        $cacheKeyPrefix = 'orders_data_cache_chunk_';
        $chunkCount = Cache::get('orders_data_cache_chunks', 0);

        if ($chunkCount === 0) {
            return response()->json([
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ]);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchQuery = $request->input('searchQuery', null);

        $chunkData = Cache::get($cacheKeyPrefix . '1', collect());
        $recordsPerChunk = $chunkData->count();

        if ($recordsPerChunk === 0) {
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

        return response()->json([
            'data' => $filteredData->values()->toArray(),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ]);
    }
}
