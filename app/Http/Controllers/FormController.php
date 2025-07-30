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
            'dokumen' => 'required|file|mimes:doc,docx,pdf|max:10240', // max 10 MB
            'bukti' => 'required|file|mimes:jpg,png,jpeg|max:5120', // max 5 MB
        ]);

        try {
            // Ambil data form
            $layanan = $request->layanan;
            $nama    = $request->nama;
            $email   = $request->email;
            $nomor   = $request->no_hp;
            $univ    = $request->univ;

            // Simpan file ke storage/public
            $dokumenPath = $request->file('dokumen')->store('uploads/dokumen', 'public');
            $buktiPath   = $request->file('bukti')->store('uploads/bukti', 'public');

            // Buat URL file
            $dokumenUrl = asset('storage/' . $dokumenPath);
            $buktiUrl   = asset('storage/' . $buktiPath);

            // Format pesan utama
            $pesan = <<<EOT
📄 *Form Pemesanan Educheck*
Layanan: {$layanan}
Nama: {$nama}
Email: {$email}
No HP: {$nomor}
Universitas: {$univ}
EOT;

            // Nomor tujuan WA Admin
            $adminNumber = '6285268360526';

            // Ambil token dari .env
            $token = env('FONNTE_TOKEN');

            if (!$token) {
                throw new \Exception("Token Fonnte belum diset di .env");
            }

            // Kirim pesan teks
            $res1 = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => $pesan,
                'delay' => 2,
            ])->throw();

            // Kirim dokumen
            $res2 = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => "📎 Dokumen dari {$nama}",
                'url' => $dokumenUrl,
                'delay' => 4,
            ])->throw();

            // Kirim bukti transfer
            $res3 = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => "📎 Bukti Transfer dari {$nama}",
                'url' => $buktiUrl,
                'delay' => 6,
            ])->throw();

            return redirect()->back()->with('success', 'Form berhasil dikirim dan file sudah masuk ke WhatsApp Admin.');

        } catch (\Throwable $e) {
            Log::error('Gagal kirim Fonnte: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengirim ke WhatsApp. Silakan coba beberapa saat lagi.');
        }
    }
}
