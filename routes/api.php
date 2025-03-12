<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;


Route::get('/superwalltez/webhook', function () {
    echo "Prueba";
});

// Recibimos el webhook de SuperWalltez
Route::post('/superwalltez/webhook', [TransactionController::class, 'webhookSuperWalltez']);
