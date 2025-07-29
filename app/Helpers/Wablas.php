<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Wablas
{
    protected static function getEndpoint($path)
    {
        return rtrim(env('WABLAS_ENDPOINT'), '/') . $path;
    }

    public static function sendText($phone, $message)
    {
        $response = Http::asForm()->post(self::getEndpoint('/api/send-message'), [
            'token'   => env('WABLAS_TOKEN'),
            'phone'   => $phone,
            'message' => $message,
        ]);

        return $response->json();
    }

    public static function sendFile($phone, $caption, $fileUrl)
    {
        $response = Http::asForm()->post(self::getEndpoint('/api/send-document'), [
            'token'    => env('WABLAS_TOKEN'),
            'phone'    => $phone,
            'caption'  => $caption,
            'url'      => $fileUrl,
            'filename' => basename($fileUrl),
        ]);

        return $response->json();
    }
}
