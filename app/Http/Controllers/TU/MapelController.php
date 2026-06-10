<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\KelompokMapel;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = Mapel::with('kelompokMapel');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_mapel', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelompok_mapel_id')) {
            $query->where('kelompok_mapel_id', $request->kelompok_mapel_id);
        }

        $perPage = $request->input('per_page', 20);
        $mapel = $query
            ->orderByRaw('CASE WHEN urutan IS NULL OR urutan = 0 THEN 1 ELSE 0 END')
            ->orderBy('urutan');

        if ($perPage === 'all') {
            $mapel = $mapel->get();
        } else {
            $mapel = $mapel->paginate((int) $perPage)->withQueryString();
        }

        $kelompok = KelompokMapel::orderBy('nama')->get();

        return view('tu.mapel.index', compact('mapel', 'kelompok'));
    }

    public function create()
    {
        $kelompok = KelompokMapel::orderBy('nama')->get();

        return view('tu.mapel.form', ['mapel' => new Mapel, 'kelompok' => $kelompok]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelompok_mapel_id' => ['required', 'exists:kelompok_mapel,id'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:mapel,kode'],
            'nama_mapel' => ['required', 'string', 'max:200'],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $mapel = Mapel::create($validated);

        activity()
            ->performedOn($mapel)
            ->event('created')
            ->withProperties(['nama' => $mapel->nama_mapel])
            ->log('Mata pelajaran ditambahkan');

        return redirect()->route('tu.mapel.index')->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Mapel $mapel)
    {
        $kelompok = KelompokMapel::orderBy('nama')->get();

        return view('tu.mapel.form', compact('mapel', 'kelompok'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $validated = $request->validate([
            'kelompok_mapel_id' => ['required', 'exists:kelompok_mapel,id'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:mapel,kode,'.$mapel->id],
            'nama_mapel' => ['required', 'string', 'max:200'],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $mapel->update($validated);

        activity()
            ->performedOn($mapel)
            ->event('updated')
            ->withProperties(['nama' => $mapel->nama_mapel])
            ->log('Mata pelajaran diperbarui');

        return redirect()->route('tu.mapel.index')->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        $nama = $mapel->nama_mapel;
        $mapel->delete();

        activity()
            ->performedOn($mapel)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Mata pelajaran dihapus');

        return back()->with('status', 'Mata pelajaran berhasil dihapus.');
    }

    public function updateBatch(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:mapel,id'],
            'items.*.kelompok_mapel_id' => ['required', 'exists:kelompok_mapel,id'],
            'items.*.kkm' => ['required', 'integer', 'min:0', 'max:100'],
            'items.*.urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($validated['items'] as $item) {
            Mapel::where('id', $item['id'])->update([
                'kelompok_mapel_id' => $item['kelompok_mapel_id'],
                'kkm' => $item['kkm'],
                'urutan' => $item['urutan'],
            ]);
        }

        return back()->with('status', count($validated['items']).' data berhasil diperbarui.');
    }
}
