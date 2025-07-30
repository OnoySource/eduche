<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    public function prosesForm(Request $request)
    {
        // Validasi inputan form
        $request->validate([
            'layanan' => 'required|in:turnitin,parafrase',
            'nama' => 'required|string|min:4|max:30',
            'email' => 'required|email',
            'no_hp' => 'required|numeric|digits_between:10,13',
            'univ' => 'required|min:7|max:30',
            'dokumen' => 'required|file|mimes:doc,docx,pdf|max:10240',
            'bukti' => 'required|file|mimes:jpg,png|max:5120',
        ]);

        try {
            // Ambil semua inputan
            $layanan = $request->input('layanan');
            $nama    = $request->input('nama');
            $email   = $request->input('email');
            $nomor   = $request->input('no_hp');
            $univ    = $request->input('univ');

            // Simpan file ke storage publik
            $dokumenPath = $request->file('dokumen')->store('uploads/dokumen', 'public');
            $buktiPath   = $request->file('bukti')->store('uploads/bukti', 'public');

            // Generate link file publik
            $dokumenUrl = asset('storage/' . $dokumenPath);
            $buktiUrl   = asset('storage/' . $buktiPath);

            // Format pesan teks
            $pesan = "📄 *Form Pemesanan Educheck*\n"
                   . "Layanan: {$layanan}\n"
                   . "Nama: {$nama}\n"
                   . "Email: {$email}\n"
                   . "No HP: {$nomor}\n"
                   . "Universitas: {$univ}";

            // Nomor tujuan WA
            $adminNumber = '6285268360526'; // ← Ganti dengan nomor admin kamu

            // Ambil token dari .env
            $token = env('FONNTE_TOKEN');

            // Kirim pesan teks
            Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => $pesan,
            ]);

            // Kirim dokumen
            Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => "📎 Dokumen dari {$nama}",
                'url' => $dokumenUrl,
            ]);

            // Kirim bukti transfer
            Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => "📎 Bukti Transfer dari {$nama}",
                'url' => $buktiUrl,
            ]);

            return redirect()->back()->with('success', 'Form berhasil dikirim dan file berhasil masuk ke WhatsApp Admin.');
        } catch (\Exception $e) {
            Log::error('Gagal kirim Fonnte: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim ke WhatsApp. Coba lagi nanti.');
        }
    }
}
