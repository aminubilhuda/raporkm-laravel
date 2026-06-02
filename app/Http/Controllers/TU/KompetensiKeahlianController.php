<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\KompetensiKeahlian;
use Illuminate\Http\Request;

class KompetensiKeahlianController extends Controller
{
    public function index()
    {
        $kompetensi = KompetensiKeahlian::latest()->get();

        return view('tu.kompetensi.index', compact('kompetensi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kompetensi_keahlian,nama'],
            'singkatan' => ['nullable', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string'],
        ]);

        KompetensiKeahlian::create($validated);

        return back()->with('status', 'Kompetensi keahlian berhasil ditambahkan.');
    }

    public function update(Request $request, KompetensiKeahlian $kompetensiKeahlian)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kompetensi_keahlian,nama,'.$kompetensiKeahlian->id],
            'singkatan' => ['nullable', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $kompetensiKeahlian->update($validated);

        return back()->with('status', 'Kompetensi keahlian berhasil diperbarui.');
    }

    public function destroy(KompetensiKeahlian $kompetensiKeahlian)
    {
        $kompetensiKeahlian->delete();

        return back()->with('status', 'Kompetensi keahlian berhasil dihapus.');
    }
}
