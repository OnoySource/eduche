<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // Upload file
        $dokumenPath = $request->file('dokumen')->store('dokumen', 'public');
        $buktiPath   = $request->file('bukti')->store('bukti', 'public');

        // Buat link file
        $linkDokumen = asset('storage/' . $dokumenPath);
        $linkBukti   = asset('storage/' . $buktiPath);

        // Format pesan WhatsApp
        $pesan = "Halo Admin%0ASaya ingin menggunakan jasa {$layanan}.%0A%0ANama: {$nama}%0AEmail: {$email}%0ANo HP: {$nomor}%0AUniversitas: {$univ}%0A%0ALink Dokumen: {$linkDokumen}%0ALink Bukti Transfer: {$linkBukti}";

        // Nomor admin WhatsApp
        $noWaAdmin = "6285268360526"; // ganti dengan nomor kamu

        // Redirect ke WhatsApp
        return redirect()->away("https://wa.me/{$noWaAdmin}?text={$pesan}");
    }
}
