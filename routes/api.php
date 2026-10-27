<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/low-stock', [ProductController::class, 'lowStock']);

// Orders
Route::post('/orders', [OrderController::class, 'store']);

// Customer order history
Route::get('/customers/{email}/orders', [OrderController::class, 'customerHistory']);
