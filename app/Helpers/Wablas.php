<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Wablas
{
    public static function sendText($phone, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => env('WABLAS_API_KEY'),
        ])->post('https://send.wablas.com/api/v2/send-message', [
            'phone' => $phone,
            'message' => $message,
            'device' => env('WABLAS_DEVICE_PHONE'),
        ]);

        return $response->json();
    }

    public static function sendFile($phone, $caption, $fileUrl)
    {
        $response = Http::withHeaders([
            'Authorization' => env('WABLAS_API_KEY'),
        ])->post('https://send.wablas.com/api/v2/send-document', [
            'phone' => $phone,
            'caption' => $caption,
            'url' => $fileUrl,
            'filename' => 'dokumen.pdf',
            'device' => env('WABLAS_DEVICE_PHONE'),
        ]);

        return $response->json();
    }
}
