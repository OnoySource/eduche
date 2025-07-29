<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Wablas
{
    public static function sendText($phone, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => env('WABLAS_API_KEY'),
        ])->post(env('WABLAS_URL') . '/api/v2/send-message', [
            'phone' => $phone,
            'message' => $message,
            'device' => env('WABLAS_DEVICE_PHONE'), // hilangkan jika tidak butuh
        ]);

        return $response->json();
    }

    public static function sendFile($phone, $caption, $fileUrl)
    {
        $response = Http::withHeaders([
            'Authorization' => env('WABLAS_API_KEY'),
        ])->post(env('WABLAS_URL') . '/api/v2/send-document', [
            'phone'    => $phone,
            'caption'  => $caption,
            'url'      => $fileUrl,
            'filename' => basename($fileUrl),
            'device'   => env('WABLAS_DEVICE_PHONE'), // hilangkan jika tidak butuh
        ]);

        return $response->json();
    }
}
