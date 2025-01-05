<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MailController;

/*
|---------------------------------------------------------------------------
| Web Routes
|---------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-test-email', [MailController::class, 'sendTestEmail']);
Route::get('/get-search-orders', [OrderController::class, 'getSearchData'])->name('get-search-orders');
// All Orders Route
Route::get('/all-orders', [OrderController::class, 'index']);

// Get Orders with Filtering (Search)
Route::get('/search-orders', [OrderController::class, 'searchPage']);

// Get All Orders without Filtering (use this for unfiltered data)
Route::get('/get-all-orders', [OrderController::class, 'getAllData']);

// Fetch the Customers (for Select2 or search functionality)
Route::get('/get-customers', [OrderController::class, 'getCustomers']); // Consolidate fetch-customers into get-customers

