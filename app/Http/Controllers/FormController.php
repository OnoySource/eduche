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
        // Validasi input
        $request->validate([
            'layanan' => 'required|in:turnitin,parafrase',
            'nama' => 'required|string|min:4|max:30',
            'email' => 'required|email',
            'no_hp' => 'required|numeric|digits_between:10,13',
            'univ' => 'required|min:7|max:30',
            'dokumen' => 'required|file|mimes:doc,docx,pdf|max:10240', // 10 MB
            'bukti' => 'required|file|mimes:jpg,png,jpeg|max:5120',   // 5 MB
        ]);

        try {
            // Ambil data form
            $layanan = $request->layanan;
            $nama    = $request->nama;
            $email   = $request->email;
            $nomor   = $request->no_hp;
            $univ    = $request->univ;

            // Generate nama file unik
            $dokumenName = uniqid('dokumen_') . '.' . $request->file('dokumen')->getClientOriginalExtension();
            $buktiName   = uniqid('bukti_') . '.' . $request->file('bukti')->getClientOriginalExtension();

            // Simpan file ke storage (disk 'public')
            $request->file('dokumen')->storeAs('uploads/dokumen', $dokumenName, 'public');
            $request->file('bukti')->storeAs('uploads/bukti', $buktiName, 'public');

            // Buat URL file untuk dikirim via API
            $dokumenUrl = asset('storage/uploads/dokumen/' . $dokumenName);
            $buktiUrl   = asset('storage/uploads/bukti/' . $buktiName);

            // Pesan utama ke admin
            $pesan = <<<EOT
📄 *Form Pemesanan Educheck*
Layanan: {$layanan}
Nama: {$nama}
Email: {$email}
No HP: {$nomor}
Universitas: {$univ}
EOT;

            // Token dan nomor admin
            $adminNumber = '6285268360526';
            $token = env('FONNTE_TOKEN');

            if (!$token) {
                throw new \Exception("Token Fonnte belum diset di .env");
            }

            // Kirim pesan teks
            Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => $pesan,
                'delay' => 2,
            ])->throw();

            // Kirim file dokumen
            Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $adminNumber,
                'message' => "📎 Dokumen dari {$nama}",
                'url' => $dokumenUrl,
                'delay' => 4,
            ])->throw();

            // Kirim bukti transfer
            Http::withHeaders([
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
