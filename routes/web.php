<?php

use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/billing');
});

Route::get('/billing', [BillingController::class, 'index'])->name('billing');
