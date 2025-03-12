<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EasyMoneyService
{
    public function pay($amount, $currency)
    {
        $response = Http::post('http://host.docker.internal:3000/process', [
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return $response;
    }
}
