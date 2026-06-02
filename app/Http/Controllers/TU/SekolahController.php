<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

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
        ]);

        $sekolah->update($validated);

        return back()->with('status', 'Profil sekolah berhasil diperbarui.');
    }
}
