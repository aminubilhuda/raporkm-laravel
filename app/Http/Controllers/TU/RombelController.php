<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Tingkat;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index()
    {
        $rombels = Kelas::with(['tingkat', 'kompetensiKeahlian'])->latest()->paginate(15);
        $tingkats = Tingkat::orderBy('urutan')->get();
        $kompetensis = KompetensiKeahlian::orderBy('nama')->get();

        return view('tu.rombel.index', compact('rombels', 'tingkats', 'kompetensis'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Kelas::class);

        $validated = $request->validate([
            'tingkat_id' => ['required', 'exists:tingkat,id'],
            'kompetensi_keahlian_id' => ['required', 'exists:kompetensi_keahlian,id'],
            'nama_kelas' => ['required', 'string', 'max:50'],
        ]);

        $kelas = Kelas::create($validated);

        activity()
            ->performedOn($kelas)
            ->event('created')
            ->withProperties(['nama' => $kelas->nama_kelas])
            ->log('Rombel ditambahkan');

        return back()->with('status', 'Rombel berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $rombel)
    {
        $this->authorize('update', $rombel);

        $validated = $request->validate([
            'tingkat_id' => ['required', 'exists:tingkat,id'],
            'kompetensi_keahlian_id' => ['required', 'exists:kompetensi_keahlian,id'],
            'nama_kelas' => ['required', 'string', 'max:50'],
        ]);

        $rombel->update($validated);

        activity()
            ->performedOn($rombel)
            ->event('updated')
            ->withProperties(['nama' => $rombel->nama_kelas])
            ->log('Rombel diperbarui');

        return back()->with('status', 'Rombel berhasil diperbarui.');
    }

    public function destroy(Kelas $rombel)
    {
        $this->authorize('delete', $rombel);

        $nama = $rombel->nama_kelas;
        $rombel->delete();

        activity()
            ->performedOn($rombel)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Rombel dihapus');

        return back()->with('status', 'Rombel berhasil dihapus.');
    }
}
