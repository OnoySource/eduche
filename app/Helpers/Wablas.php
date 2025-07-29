<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Wablas
{
    public static function sendText($phone, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => config('services.wablas.api_key'),
        ])->post('https://send.wablas.com/api/v2/send-message', [
            'phone' => $phone,
            'message' => $message,
            'device' => config('services.wablas.device'),
        ]);

        return $response->json();
    }

    public static function sendFile($phone, $caption, $fileUrl)
    {
        $response = Http::withHeaders([
            'Authorization' => config('services.wablas.api_key'),
        ])->post('https://send.wablas.com/api/v2/send-document', [
            'phone' => $phone,
            'caption' => $caption,
            'url' => $fileUrl,
            'filename' => basename($fileUrl),
            'device' => config('services.wablas.device'),
        ]);

        return $response->json();
    }
}
  