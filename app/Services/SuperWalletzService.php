<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SuperWalletzService
{
    public function pay($amount, $currency)
    {

        $callbackUrl = "http://localhost:8000/api/superwalltez/webhook";

        $response = Http::post('http://host.docker.internal:3003/pay', [
            'amount' => $amount,
            'currency' => $currency,
            'callback_url' => $callbackUrl,
        ]);

        return $response;
    }
}
