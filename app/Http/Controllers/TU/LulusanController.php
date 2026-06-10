<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Lulusan\StoreLulusanRequest;
use App\Http\Requests\TU\Lulusan\UpdateLulusanRequest;
use App\Models\Kelas;
use App\Models\Lulusan;
use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;

class LulusanController extends Controller
{
    public function index(Request $request)
    {
        $sekolah = Sekolah::first();
        $tpId = session('selected_tahun', $sekolah?->tahun_aktif);
        $semesterId = session('selected_semester', $sekolah?->semester_aktif);

        $query = Lulusan::with(['siswa', 'kelas'])
            ->where('tahun_pelajaran_id', $tpId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('siswa', function ($qs) use ($search) {
                    $qs->where('nama_siswa', 'like', "%{$search}%");
                })->orWhere('no_ijazah', 'like', "%{$search}%")
                    ->orWhere('lanjut_ke', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $lulusans = $query->latest()->get();
        } else {
            $lulusans = $query->latest()->paginate((int) $perPage)->withQueryString();
        }

        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('tu.lulusan.index', compact('lulusans', 'siswas', 'kelass'));
    }

    public function store(StoreLulusanRequest $r)
    {
        $sekolah = Sekolah::first();
        $data = $r->validated();
        $data['tahun_pelajaran_id'] = $data['tahun_pelajaran_id'] ?? session('selected_tahun', $sekolah?->tahun_aktif);
        $data['semester_id'] = $data['semester_id'] ?? session('selected_semester', $sekolah?->semester_aktif);

        $lulusan = Lulusan::create($data);

        activity()
            ->performedOn($lulusan)
            ->event('created')
            ->withProperties(['nama' => $lulusan->siswa->nama_siswa ?? ''])
            ->log('Lulusan ditambahkan');

        return back()->with('status', 'Data kelulusan ditambahkan.');
    }

    public function update(UpdateLulusanRequest $r, Lulusan $lulusan)
    {
        $lulusan->update($r->validated());

        activity()
            ->performedOn($lulusan)
            ->event('updated')
            ->withProperties(['nama' => $lulusan->siswa->nama_siswa ?? ''])
            ->log('Lulusan diperbarui');

        return back()->with('status', 'Diperbarui.');
    }

    public function destroy(Lulusan $lulusan)
    {
        $nama = $lulusan->siswa->nama_siswa ?? '';
        $lulusan->delete();

        activity()
            ->performedOn($lulusan)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Lulusan dihapus');

        return back()->with('status', 'Dihapus.');
    }
}
