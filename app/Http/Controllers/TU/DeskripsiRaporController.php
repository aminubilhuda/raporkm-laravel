<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\DeskripsiRapor;
use Illuminate\Http\Request;

class DeskripsiRaporController extends Controller
{
    public function index()
    {
        $deskripsi = DeskripsiRapor::latest()->get();

        return view('tu.deskripsi-rapor.index', compact('deskripsi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kktp' => ['required', 'integer', 'min:0', 'max:100'],
            'predikat' => ['required', 'string', 'max:50'],
            'deskripsi' => ['required', 'string'],
        ]);

        DeskripsiRapor::create($validated);

        return back()->with('status', 'Template deskripsi berhasil ditambahkan.');
    }

    public function update(Request $request, DeskripsiRapor $deskripsiRapor)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kktp' => ['required', 'integer', 'min:0', 'max:100'],
            'predikat' => ['required', 'string', 'max:50'],
            'deskripsi' => ['required', 'string'],
        ]);

        $deskripsiRapor->update($validated);

        return back()->with('status', 'Template deskripsi berhasil diperbarui.');
    }

    public function destroy(DeskripsiRapor $deskripsiRapor)
    {
        $deskripsiRapor->delete();

        return back()->with('status', 'Template deskripsi berhasil dihapus.');
    }
}
