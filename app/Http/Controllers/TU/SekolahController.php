<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::first();

        if (! $sekolah) {
            $sekolah = Sekolah::create(['npsn' => '', 'nama_sekolah' => 'SMK Abdi Negara Tuban']);
        }

        return view('tu.sekolah.index', compact('sekolah'));
    }

    public function update(Request $request)
    {
        $sekolah = Sekolah::firstOrFail();

        $validated = $request->validate([
            'npsn' => ['required', 'string', 'max:20'],
            'nama_sekolah' => ['required', 'string', 'max:200'],
            'alamat' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:20'],
            'desa' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'hapus_logo' => ['nullable', 'boolean'],
        ]);

        // Handle hapus logo
        if ($request->boolean('hapus_logo')) {
            if ($sekolah->logo) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            if ($sekolah->favicon) {
                Storage::disk('public')->delete($sekolah->favicon);
            }
            $validated['logo'] = null;
            $validated['favicon'] = null;
        }
        unset($validated['hapus_logo']);

        // Handle upload logo baru
        if ($request->hasFile('logo')) {
            // Hapus file lama
            if ($sekolah->logo) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            if ($sekolah->favicon) {
                Storage::disk('public')->delete($sekolah->favicon);
            }

            // Simpan logo
            $logoPath = $request->file('logo')->store('sekolah', 'public');
            $validated['logo'] = $logoPath;

            // Auto-generate favicon
            $faviconPath = $this->generateFavicon($request->file('logo'));
            $validated['favicon'] = $faviconPath;
        }

        $sekolah->update($validated);

        return back()->with('status', 'Profil sekolah berhasil diperbarui.');
    }

    private function generateFavicon(UploadedFile $file): string
    {
        $sourcePath = $file->getRealPath();
        $imageInfo = getimagesize($sourcePath);

        if ($imageInfo === false) {
            return '';
        }

        $mime = $imageInfo['mime'];
        $size = 64;

        // Buat canvas baru
        $newImage = imagecreatetruecolor($size, $size);

        // Load source image
        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($sourcePath);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;
            default:
                return '';
        }

        if (! $source) {
            imagedestroy($newImage);

            return '';
        }

        // Resize dengan kualitas baik
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $size, $size, $imageInfo[0], $imageInfo[1]);

        // Simpan sebagai PNG
        $faviconPath = 'sekolah/favicon.png';
        $fullPath = Storage::disk('public')->path($faviconPath);

        // Pastikan directory ada
        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagepng($newImage, $fullPath);

        // Cleanup
        imagedestroy($source);
        imagedestroy($newImage);

        return $faviconPath;
    }
}
