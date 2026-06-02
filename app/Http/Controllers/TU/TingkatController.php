<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Tingkat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TingkatController extends Controller
{
    public function index()
    {
        $tingkat = Tingkat::orderBy('urutan')->get();

        return view('tu.tingkat.index', compact('tingkat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:10'],
            'angka' => ['required', 'integer', 'unique:tingkat,angka'],
            'fase' => ['required', 'string', 'max:5'],
            'urutan' => ['required', 'integer'],
        ]);

        Tingkat::create($validated);

        return back()->with('status', 'Tingkat berhasil ditambahkan.');
    }

    public function update(Request $request, Tingkat $tingkat)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:10'],
            'angka' => ['required', 'integer', Rule::unique('tingkat', 'angka')->ignore($tingkat->id)],
            'fase' => ['required', 'string', 'max:5'],
            'urutan' => ['required', 'integer'],
        ]);

        $tingkat->update($validated);

        return back()->with('status', 'Tingkat berhasil diperbarui.');
    }

    public function destroy(Tingkat $tingkat)
    {
        $tingkat->delete();

        return back()->with('status', 'Tingkat berhasil dihapus.');
    }
}
