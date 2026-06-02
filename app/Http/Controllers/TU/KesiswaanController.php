<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KesiswaanController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('aktif', 1)->latest()->paginate(15);

        return view('tu.kesiswaan.index', compact('siswa'));
    }

    public function create()
    {
        return view('tu.kesiswaan.form', ['siswa' => new Siswa]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:100'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')],
            'nis' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nis')],
            'nik_pd' => ['nullable', 'string', 'max:20'],
            'nkk' => ['nullable', 'string', 'max:20'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
            'kontak_siswa' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:100'],
            'nik_ayah' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:30'],
            'kontak_ayah' => ['nullable', 'string', 'max:14'],
            'nama_ibu' => ['nullable', 'string', 'max:100'],
            'nik_ibu' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:30'],
            'kontak_ibu' => ['nullable', 'string', 'max:14'],
            'alamat_orang_tua' => ['nullable', 'string'],
            'nama_wali' => ['nullable', 'string', 'max:100'],
            'pekerjaan_wali' => ['nullable', 'string', 'max:30'],
            'kontak_wali' => ['nullable', 'string', 'max:14'],
            'sekolah_asal' => ['nullable', 'string'],
            'jenis_siswa' => ['nullable', 'integer'],
        ]);

        Siswa::create($validated);

        return redirect()->route('tu.kesiswaan.index')->with('status', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $kesiswaan)
    {
        return view('tu.kesiswaan.show', ['siswa' => $kesiswaan]);
    }

    public function edit(Siswa $kesiswaan)
    {
        return view('tu.kesiswaan.form', ['siswa' => $kesiswaan]);
    }

    public function update(Request $request, Siswa $kesiswaan)
    {
        $validated = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:100'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($kesiswaan->id)],
            'nis' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nis')->ignore($kesiswaan->id)],
            'nik_pd' => ['nullable', 'string', 'max:20'],
            'nkk' => ['nullable', 'string', 'max:20'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
            'kontak_siswa' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:100'],
            'nik_ayah' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:30'],
            'kontak_ayah' => ['nullable', 'string', 'max:14'],
            'nama_ibu' => ['nullable', 'string', 'max:100'],
            'nik_ibu' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:30'],
            'kontak_ibu' => ['nullable', 'string', 'max:14'],
            'alamat_orang_tua' => ['nullable', 'string'],
            'nama_wali' => ['nullable', 'string', 'max:100'],
            'pekerjaan_wali' => ['nullable', 'string', 'max:30'],
            'kontak_wali' => ['nullable', 'string', 'max:14'],
            'sekolah_asal' => ['nullable', 'string'],
            'jenis_siswa' => ['nullable', 'integer'],
            'aktif' => ['nullable', 'integer'],
        ]);

        $kesiswaan->update($validated);

        return redirect()->route('tu.kesiswaan.index')->with('status', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $kesiswaan)
    {
        $kesiswaan->update(['aktif' => 0]);
        $kesiswaan->delete();

        return back()->with('status', 'Siswa berhasil dinonaktifkan.');
    }
}
