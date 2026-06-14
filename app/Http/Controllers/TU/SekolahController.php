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

        // Strip seconds dari time fields (07:00:00 → 07:00) agar lolos validasi H:i
        foreach (['jam_masuk', 'jam_pulang'] as $field) {
            if ($request->filled($field) && strlen($request->input($field)) === 8) {
                $request->merge([$field => substr($request->input($field), 0, 5)]);
            }
        }

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
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'radius_absen' => ['nullable', 'integer', 'min:10', 'max:5000'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'hapus_logo' => ['nullable', 'boolean'],
            'logo_prov' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'hapus_logo_prov' => ['nullable', 'boolean'],
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

        // Handle hapus logo provinsi
        if ($request->boolean('hapus_logo_prov')) {
            if ($sekolah->logo_prov) {
                Storage::disk('public')->delete($sekolah->logo_prov);
            }
            $validated['logo_prov'] = null;
        }
        unset($validated['hapus_logo_prov']);

        // Handle upload logo baru
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            try {
                if ($sekolah->logo) {
                    Storage::disk('public')->delete($sekolah->logo);
                }
                if ($sekolah->favicon) {
                    Storage::disk('public')->delete($sekolah->favicon);
                }

                $logoPath = $request->file('logo')->store('sekolah', 'public');

                if (! $logoPath) {
                    return back()->with('error', 'Logo gagal disimpan. Silakan coba lagi.');
                }

                $validated['logo'] = $logoPath;

                try {
                    $faviconPath = $this->generateFavicon($request->file('logo'));
                    if ($faviconPath) {
                        $validated['favicon'] = $faviconPath;
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            } catch (\Throwable $e) {
                report($e);

                return back()->with('error', 'Upload logo gagal: ' . $e->getMessage());
            }
        }

        // Handle upload logo provinsi
        if ($request->hasFile('logo_prov') && $request->file('logo_prov')->isValid()) {
            try {
                if ($sekolah->logo_prov) {
                    Storage::disk('public')->delete($sekolah->logo_prov);
                }

                $logoProvPath = $request->file('logo_prov')->store('sekolah', 'public');

                if (! $logoProvPath) {
                    return back()->with('error', 'Logo provinsi gagal disimpan. Silakan coba lagi.');
                }

                $validated['logo_prov'] = $logoProvPath;
            } catch (\Throwable $e) {
                report($e);

                return back()->with('error', 'Upload logo provinsi gagal: ' . $e->getMessage());
            }
        }

        $sekolah->update($validated);

        return back()->with('status', 'Profil sekolah berhasil diperbarui.');
    }

    private function generateFavicon(UploadedFile $file): string
    {
        $sourcePath = $file->getRealPath();
        $imageInfo = @getimagesize($sourcePath);

        if ($imageInfo === false) {
            return '';
        }

        $mime = $imageInfo['mime'];
        $size = 64;

        $newImage = imagecreatetruecolor($size, $size);

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

        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $size, $size, $imageInfo[0], $imageInfo[1]);

        $faviconPath = 'sekolah/favicon.png';

        ob_start();
        imagepng($newImage);
        $imageBinary = ob_get_clean();

        Storage::disk('public')->put($faviconPath, $imageBinary);

        imagedestroy($source);
        imagedestroy($newImage);

        return $faviconPath;
    }
}
