<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class Wablas
{
    protected static function getEndpoint($path)
    {
        return rtrim(Config::get('services.wablas.endpoint'), '/') . $path;
    }

    protected static function getHeaders()
    {
        return [
            'Authorization' => Config::get('services.wablas.api_key'),
        ];
    }

    public static function sendText($phone, $message)
    {
        $response = Http::withHeaders(self::getHeaders())
            ->post(self::getEndpoint('/api/v2/send-message'), [
                'phone'  => $phone,
                'message'=> $message,
                'device' => Config::get('services.wablas.device'),
            ]);

        return $response->json();
    }

    public static function sendFile($phone, $caption, $fileUrl)
    {
        $response = Http::withHeaders(self::getHeaders())
            ->post(self::getEndpoint('/api/v2/send-document'), [
                'phone'    => $phone,
                'caption'  => $caption,
                'url'      => $fileUrl,
                'filename' => basename($fileUrl),
                'device'   => Config::get('services.wablas.device'),
            ]);

        return $response->json();
    }
}
