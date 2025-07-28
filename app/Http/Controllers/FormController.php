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
    \Log::info("Mencoba upload file: " . $file->getClientOriginalName());

    // Simpan ke Google Drive
    $path = $file->store('', 'google');

    \Log::info("Path setelah upload: " . $path);

    if (!$path) {
        \Log::error("Gagal upload file ke Google Drive.");
        throw new \Exception("Gagal upload file.");
    }

    // Ambil metadata file
    $googleDrive = Storage::disk('google');
    $adapter = $googleDrive->getAdapter();
    $service = $adapter->getService();

    $metadata = $adapter->getMetadata($path);

    if (!$metadata || !isset($metadata['extraMetadata']['id'])) {
        \Log::error("Gagal mendapatkan metadata dari path: " . $path);
        throw new \Exception("Upload ke Google Drive gagal.");
    }

    $fileId = $metadata['extraMetadata']['id'];

    // Jadikan file publik
    $permission = new \Google_Service_Drive_Permission();
    $permission->setType('anyone');
    $permission->setRole('reader');
    $service->permissions->create($fileId, $permission);

    return "https://drive.google.com/file/d/{$fileId}/view";
}

}
