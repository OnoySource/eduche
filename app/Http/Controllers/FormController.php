<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    public function prosesForm(Request $request)
    {
        // Validasi inputan
        $validated = $request->validate([
            'layanan'   => ['required', 'in:turnitin,parafrase'],
            'nama'      => ['required', 'string', 'min:4', 'max:30'],
            'email'     => ['required', 'email'],
            'no_hp'     => ['required', 'numeric', 'digits_between:10,13'],
            'univ'      => ['required', 'min:7', 'max:30'],
            'dokumen'   => ['required', 'file', 'mimes:doc,docx,pdf', 'max:10240'], // max 10 MB
            'bukti'     => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],  // max 5 MB
        ]);

        try {
            // Simpan file ke public storage
            $dokumenPath = $request->file('dokumen')->store('uploads/dokumen', 'public');
            $buktiPath   = $request->file('bukti')->store('uploads/bukti', 'public');

            // Buat URL publik ke file (pastikan APP_URL aktif)
            $dokumenUrl = url('storage/' . $dokumenPath);
            $buktiUrl   = url('storage/' . $buktiPath);

            // Format pesan utama
            $pesanUtama = <<<MSG
📄 Form Pemesanan Educheck
Layanan: {$validated['layanan']}
Nama: {$validated['nama']}
Email: {$validated['email']}
No HP: {$validated['no_hp']}
Universitas: {$validated['univ']}

📎 Dokumen: $dokumenUrl
📎 Bukti Transfer: $buktiUrl

> Sent via fonnte.com
MSG;

            // Nomor admin tujuan
            $adminNumber = '6285268360526';

            // Kirim semua data sekaligus
            $this->sendFonnte($adminNumber, $pesanUtama);

            return back()->with('success', 'Form berhasil dikirim dan file berhasil masuk ke WhatsApp Admin.');

        } catch (\Throwable $e) {
            Log::error('Gagal kirim via Fonnte: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ke WhatsApp. Silakan coba beberapa saat lagi.');
        }
    }

    private function sendFonnte(string $target, string $message): void
    {
        $token = env('FONNTE_TOKEN');

        if (! $token) {
            throw new \Exception("FONNTE_TOKEN belum diatur di file .env");
        }

        Http::asForm()
            ->withHeader('Authorization', $token)
            ->post('https://api.fonnte.com/send', [
                'target'  => $target,
                'message' => $message,
                'countryCode' => '62', // pastikan default kode negara
            ])
            ->throw();
    }
}
