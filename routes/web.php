<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-test-email', [MailController::class, 'sendTestEmail']);

Route::get('/all-orders', [OrderController::class, 'index']);
Route::get('/search-orders', [OrderController::class, 'searchPage']);
Route::get('/get-all-orders', [OrderController::class, 'getAllData']);
Route::get('/get-search-orders', [OrderController::class, 'getSearchData']);