<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

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

        // Upload ke Google Drive
        $dokumenFile = $request->file('dokumen');
        $buktiFile   = $request->file('bukti');

        $dokumenPath = $this->uploadToGoogleDrive($dokumenFile);
        $buktiPath   = $this->uploadToGoogleDrive($buktiFile);

        // Format pesan WhatsApp
        $pesan = "Halo Admin%0ASaya ingin menggunakan jasa {$layanan}.%0A%0ANama: {$nama}%0AEmail: {$email}%0ANo HP: {$nomor}%0AUniversitas: {$univ}%0A%0ALink Dokumen: {$dokumenPath}%0ALink Bukti Transfer: {$buktiPath}";

        // Nomor admin WhatsApp
        $noWaAdmin = "6285268360526";

        // Redirect ke WhatsApp
        return redirect()->away("https://wa.me/{$noWaAdmin}?text={$pesan}");
    }

    protected function uploadToGoogleDrive($file)
    {
        // Simpan sementara ke storage Laravel
        $path = $file->store('', 'google'); // simpan langsung ke Google Drive root / folder

        // Ambil metadata file
        $googleDrive = Storage::disk('google');
        $adapter = $googleDrive->getAdapter();
        $service = $adapter->getService();

        // Ambil file ID
        $fileId = $adapter->getMetadata($path)['extraMetadata']['id'];

        // Jadikan file publik (agar bisa dishare)
        $permission = new \Google_Service_Drive_Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');
        $service->permissions->create($fileId, $permission);

        // Buat URL publik
        return "https://drive.google.com/file/d/{$fileId}/view";
    }
}
    