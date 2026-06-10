<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Prestasi\StorePrestasiRequest;
use App\Http\Requests\TU\Prestasi\UpdatePrestasiRequest;
use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestasi::with('siswa');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('siswa', function ($qs) use ($search) {
                    $qs->where('nama_siswa', 'like', "%{$search}%");
                })->orWhere('nama_prestasi', 'like', "%{$search}%")
                    ->orWhere('tingkat', 'like', "%{$search}%")
                    ->orWhere('penyelenggara', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $prestasis = $query->latest()->get();
        } else {
            $prestasis = $query->latest()->paginate((int) $perPage)->withQueryString();
        }

        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();

        return view('tu.prestasi.index', compact('prestasis', 'siswas'));
    }

    public function store(StorePrestasiRequest $r)
    {
        $prestasi = Prestasi::create($r->validated());

        activity()
            ->performedOn($prestasi)
            ->event('created')
            ->withProperties(['nama' => $prestasi->siswa->nama_siswa ?? ''])
            ->log('Prestasi ditambahkan');

        return back()->with('status', 'Prestasi ditambahkan.');
    }

    public function update(UpdatePrestasiRequest $r, Prestasi $prestasi)
    {
        $prestasi->update($r->validated());

        activity()
            ->performedOn($prestasi)
            ->event('updated')
            ->withProperties(['nama' => $prestasi->siswa->nama_siswa ?? ''])
            ->log('Prestasi diperbarui');

        return back()->with('status', 'Diperbarui.');
    }

    public function destroy(Prestasi $prestasi)
    {
        $nama = $prestasi->siswa->nama_siswa ?? '';
        $prestasi->delete();

        activity()
            ->performedOn($prestasi)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Prestasi dihapus');

        return back()->with('status', 'Dihapus.');
    }
}
