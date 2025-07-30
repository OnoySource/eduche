<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{
    public function prosesForm(Request $request)
    {
        // Validasi inputan form
        $validated = $request->validate([
            'layanan' => ['required', 'in:turnitin,parafrase'],
            'nama'    => ['required', 'string', 'min:4', 'max:30'],
            'email'   => ['required', 'email'],
            'no_hp'   => ['required', 'numeric', 'digits_between:10,13'],
            'univ'    => ['required', 'min:7', 'max:30'],
            'dokumen' => ['required', 'file', 'mimes:doc,docx,pdf', 'max:10240'], // max 10MB
            'bukti'   => ['required', 'file', 'mimes:jpg,png,jpeg', 'max:5120'],  // max 5MB
        ]);

        try {
            // Simpan file ke storage/app/public/uploads/...
            $dokumenPath = $request->file('dokumen')->store('uploads/dokumen', 'public');
            $buktiPath   = $request->file('bukti')->store('uploads/bukti', 'public');

            // Ambil base URL dari .env (APP_URL), fallback ke asset() kalau tidak ada
            $baseUrl = config('app.url') ?? asset('');

            // Buat URL publik ke file
            $dokumenUrl = $baseUrl . '/storage/' . $dokumenPath;
            $buktiUrl   = $baseUrl . '/storage/' . $buktiPath;

            // Format pesan utama
            $pesan = <<<TEXT
📄 Form Pemesanan Educheck
Layanan: {$validated['layanan']}
Nama: {$validated['nama']}
Email: {$validated['email']}
No HP: {$validated['no_hp']}
Universitas: {$validated['univ']}

> Sent via fonnte.com
TEXT;

            // Nomor admin WhatsApp (ganti sesuai kebutuhan)
            $adminNumber = '6285268360526';

            // Kirim pesan teks utama
            $this->sendFonnte($adminNumber, $pesan);

            // Kirim file dokumen dan bukti
            $this->sendFonnte($adminNumber, "📎 Dokumen dari {$validated['nama']}", $dokumenUrl);
            $this->sendFonnte($adminNumber, "📎 Bukti Transfer dari {$validated['nama']}", $buktiUrl);

            return back()->with('success', 'Form berhasil dikirim dan file sudah masuk ke WhatsApp Admin.');
        } catch (\Throwable $e) {
            Log::error('Gagal kirim Fonnte: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ke WhatsApp. Silakan coba beberapa saat lagi.');
        }
    }

    /**
     * Mengirim pesan ke Fonnte API
     */
    private function sendFonnte(string $target, string $message, ?string $url = null): void
    {
        $token = env('FONNTE_TOKEN');

        if (! $token) {
            throw new \Exception("Token Fonnte belum diset di .env");
        }

        Http::asForm()
            ->withHeader('Authorization', $token)
            ->throw()
            ->post('https://api.fonnte.com/send', [
                'target'  => $target,
                'message' => $message,
                'url'     => $url,
            ]);
    }
}
