<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Wablas;

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
            'dokumen' => 'required|file|mimes:doc,docx,pdf|max:10240',
            'bukti' => 'required|file|mimes:jpg,png|max:5120',
        ]);

        // Ambil data input
        $layanan = $request->input('layanan');
        $nama    = $request->input('nama');
        $email   = $request->input('email');
        $nomor   = $request->input('no_hp');
        $univ    = $request->input('univ');

        // Simpan file sementara di storage publik
        $dokumenPath = $request->file('dokumen')->store('uploads/dokumen', 'public');
        $buktiPath   = $request->file('bukti')->store('uploads/bukti', 'public');

        // Ambil URL untuk file yang bisa diakses publik
        $dokumenUrl = asset('storage/' . $dokumenPath);
        $buktiUrl   = asset('storage/' . $buktiPath);

        // Format pesan untuk admin
        $pesan = "📄 *Form Pemesanan Educheck* \n"
               . "Layanan: {$layanan}\n"
               . "Nama: {$nama}\n"
               . "Email: {$email}\n"
               . "No HP: {$nomor}\n"
               . "Universitas: {$univ}";

        $adminNumber = '6285268360526'; // Ganti dengan nomor admin

        // Kirim pesan teks
        Wablas::sendText($adminNumber, $pesan);

        // Kirim file dokumen dan bukti
        Wablas::sendFile($adminNumber, "📎 Dokumen dari {$nama}", $dokumenUrl);
        Wablas::sendFile($adminNumber, "📎 Bukti Transfer dari {$nama}", $buktiUrl);

        return redirect()->back()->with('success', 'Form berhasil dikirim dan file sudah dikirim ke WhatsApp Admin.');
    }
}
