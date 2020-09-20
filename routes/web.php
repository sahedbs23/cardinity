<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/products', [ProductController::class,'index'])->name('product_list');
Route::middleware(['auth'])->group(function (){
    Route::get('/carts', [CartController::class, 'index'])->name('cart_products');
    Route::POST('/cart', [CartController::class, 'store'])->name('add_to_cart');
    Route::DELETE('/cart/{id}', [CartController::class, 'destroy'])->name('remove_from_cart');

    Route::get('/payment',[PaymentController::class,'index'])->name('payment_form');
    Route::POST('/payment',[PaymentController::class,'store'])->name('pay_payment');
    Route::get('/payment/3dsecure',[PaymentController::class,'edit'])->name('payment_3dsecure_form');
});
Route::POST('cardinity/callback',[PaymentController::class,'update'])->name('payment_3dsecure_callback');
Route::get('cardinity/payments',[PaymentController::class,'create'])->name('payment_3dsecure_status');


