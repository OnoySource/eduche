<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function send()
    {
        $apiToken = 'nBnThHkxZwTfeC5Lm8hr'; // klik tombol Token di dashboard Fonnte
        $nomorTujuan = '6285268360526'; // atau nomor lain yang kamu mau
        $pesan = 'Halo, ini pesan otomatis dari Laravel ke WhatsApp via Fonnte API! 🎉';

        $response = Http::withHeaders([
            'Authorization' => $apiToken
        ])->asForm()->post('https://api.fonnte.com/send', [
            'target' => $nomorTujuan,
            'message' => $pesan,
        ]);

        return response()->json($response->json());
    }
}
