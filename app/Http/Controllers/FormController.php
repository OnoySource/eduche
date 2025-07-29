<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'dokumen' => 'required|file|mimes:doc,docx,pdf|max:10240',
            'bukti' => 'required|file|mimes:jpg,png|max:5120',
        ]);

        // Ambil data input
        $layanan = $request->input('layanan');
        $nama    = $request->input('nama');
        $email   = $request->input('email');
        $nomor   = $request->input('no_hp');
        $univ    = $request->input('univ');

        // Simpan file di storage lokal sementara
        $dokumenPath = $request->file('dokumen')->store('uploads/dokumen', 'public');
        $buktiPath   = $request->file('bukti')->store('uploads/bukti', 'public');

        // Ambil URL untuk file yang diupload
        $dokumenUrl = asset('storage/' . $dokumenPath);
        $buktiUrl   = asset('storage/' . $buktiPath);

        // Format pesan WhatsApp
        $pesan = "Halo Admin%0ASaya ingin menggunakan jasa {$layanan}.%0A%0ANama: {$nama}%0AEmail: {$email}%0ANo HP: {$nomor}%0AUniversitas: {$univ}%0A%0ALink Dokumen: {$dokumenUrl}%0ALink Bukti Transfer: {$buktiUrl}";

        // Nomor admin WhatsApp
        $noWaAdmin = "6285268360526";

        // Redirect ke WhatsApp
        return redirect()->away("https://wa.me/{$noWaAdmin}?text={$pesan}");
    }
}
