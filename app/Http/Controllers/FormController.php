<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    public function prosesForm(Request $request)
    {
        // Validasi inputan dengan custom error message
        $validated = $request->validate([
            'layanan'   => ['required', 'in:turnitin,parafrase'],
            'nama'      => ['required', 'string', 'min:4', 'max:30'],
            'email'     => ['required', 'email'],
            'no_hp'     => ['required', 'numeric', 'digits_between:10,13'],
            'univ'      => ['required', 'min:5', 'max:30'],
            'dokumen'   => ['required', 'file', 'mimes:doc,docx,pdf', 'max:10240'], // max 10 MB
            'bukti'     => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],  // max 5 MB
        ], [
            // Pesan error custom
            'layanan.required' => 'Silakan pilih jenis layanan.',
            'layanan.in'       => 'Layanan harus "turnitin" atau "parafrase".',

            'nama.required' => 'Nama wajib diisi.',
            'nama.min'      => 'Nama minimal 4 karakter.',
            'nama.max'      => 'Nama maksimal 30 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',

            'no_hp.required'        => 'Nomor HP wajib diisi.',
            'no_hp.numeric'         => 'Nomor HP hanya boleh angka.',
            'no_hp.digits_between'  => 'Nomor HP harus antara 10 sampai 13 digit.',

            'univ.required' => 'Universitas wajib diisi.',
            'univ.min'      => 'Universitas minimal 5 karakter.',
            'univ.max'      => 'Universitas maksimal 25 karakter.',

            'dokumen.required' => 'Silakan unggah dokumen tugas.',
            'dokumen.file'     => 'Dokumen harus berupa file.',
            'dokumen.mimes'    => 'Format dokumen harus PDF atau Word (doc/docx).',
            'dokumen.max'      => 'Ukuran dokumen maksimal 10 MB.',

            'bukti.required' => 'Silakan unggah bukti transfer.',
            'bukti.file'     => 'Bukti harus berupa file.',
            'bukti.mimes'    => 'Format bukti harus JPG atau PNG.',
            'bukti.max'      => 'Ukuran bukti maksimal 5 MB.',
        ]);

        try {
            // Simpan file ke public storage
            $dokumenPath = $request->file('dokumen')->store('uploads/dokumen', 'public');
            $buktiPath   = $request->file('bukti')->store('uploads/bukti', 'public');

            // Buat URL publik ke file
            $dokumenUrl = url('storage/' . $dokumenPath);
            $buktiUrl   = url('storage/' . $buktiPath);

            // Format pesan untuk WhatsApp
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

            // Kirim ke nomor admin
            $adminNumber = '6285268360526';

            $this->sendFonnte($adminNumber, $pesanUtama);

            return back()->with('success', '✅ Pemesanan berhasil! Silakan tunggu hasil dari tim kami.');

        } catch (\Throwable $e) {
            Log::error('Gagal kirim via Fonnte: ' . $e->getMessage());
            return back()->with('error', '❌ Pemesanan gagal. Silakan coba lagi nanti.');
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
                'countryCode' => '62',
            ])
            ->throw();
    }
}
