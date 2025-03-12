<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/deposit', [TransactionController::class, 'index']);

Route::post('/deposit', [TransactionController::class, 'deposit']);

//Route::post('/superwalletz/webhook', [TransactionController::class, 'webhookSuperWalltez']);