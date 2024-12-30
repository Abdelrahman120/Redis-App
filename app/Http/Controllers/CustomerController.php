<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class CustomerController extends Controller
{

public function checkCustomer( Request $request)
{
// $customer = Customer::where('national_id',$request->national_id)->first();
$customer=Cache::get('national_id_'.$request->national_id);
if($customer){
    //302 ms with DB ,, 198 ms with Cache
    // $customer->update($request->all());
    Customer::where('national_id', $customer)->update($request->all());
}else {
    //332 ms with DB ,, 231 ms with Cache
    Customer::create($request->all());
}


// 268 ms with Redis
// $cachedCustomerId = Redis::get('national_'.$request->national_id);
// $customer = Customer::find($cachedCustomerId);
// if ($cachedCustomerId) {
//         $customer->update($request->all());
//     } else {
//         $customer = Customer::create($request->all());
//         Redis::set('national_'.$request->national_id, $customer->id);
//     }

// return response()->json($customer, 200);

}

public function index2()
{
    $customers = Customer::select('id', 'national_id')->take(1000)->get();
    return response()->json($customers, 200);
}

// 532 ms
public function index()
{
    $customers = [];
    for ($i = 1; $i <= 1000; $i++) {
        $key = 'customer_' . $i;
        if (Cache::has($key)) {
            $cachedCustomer = Cache::get($key);
            $customers[] = [
                'id' => $cachedCustomer['id'],
                'national_id' => $cachedCustomer['national_id'],
            ];
        } else {
            break;
        }
    }
    return response()->json($customers, 200);
}

}
