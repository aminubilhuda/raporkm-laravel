<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tingkatId = $request->get('tingkat_id');
        $jurusanId = $request->get('jurusan_id');

        $sekolah = Sekolah::first();

        $rombels = Kelas::with([
            'tingkat',
            'kompetensiKeahlian',
            'wali' => fn($q) => $q
                ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
                ->where('semester_id', $sekolah?->semester_aktif)
                ->with('user'),
        ])
            ->when($search, fn($q) => $q->where('nama_kelas', 'like', "%{$search}%"))
            ->when($tingkatId, fn($q) => $q->where('tingkat_id', $tingkatId))
            ->when($jurusanId, fn($q) => $q->where('kompetensi_keahlian_id', $jurusanId))
            ->latest()->paginate(15);

        $tingkats = Tingkat::orderBy('urutan')->get();
        $kompetensis = KompetensiKeahlian::orderBy('nama')->get();
        $gurus = User::whereIn('jabatan', [3, 4])->orderBy('nama')->get();

        return view('tu.rombel.index', compact('rombels', 'tingkats', 'kompetensis', 'gurus', 'search', 'tingkatId', 'jurusanId'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Kelas::class);

        $validated = $request->validate([
            'tingkat_id' => ['required', 'exists:tingkat,id'],
            'kompetensi_keahlian_id' => ['required', 'exists:kompetensi_keahlian,id'],
            'nama_kelas' => ['required', 'string', 'max:50'],
            'wali_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $kelas = Kelas::create($validated);

        if ($validated['wali_user_id']) {
            $sekolah = Sekolah::first();
            KelasWali::create([
                'kelas_id' => $kelas->id,
                'user_id' => $validated['wali_user_id'],
                'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
                'semester_id' => $sekolah?->semester_aktif,
            ]);
        }

        activity()
            ->performedOn($kelas)
            ->event('created')
            ->withProperties(['nama' => $kelas->nama_kelas])
            ->log('Rombel ditambahkan');

        return back()->with('status', 'Rombel berhasil ditambahkan.');
    }

    public function batchSave(Request $request)
    {
        $validated = $request->validate([
            'changes' => ['required', 'array'],
            'changes.*.id' => ['required', 'exists:kelas,id'],
            'changes.*.kompetensi_keahlian_id' => ['nullable', 'exists:kompetensi_keahlian,id'],
            'changes.*.wali_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $sekolah = Sekolah::first();
        $updated = 0;

        foreach ($validated['changes'] as $change) {
            $kelas = Kelas::find($change['id']);
            if (!$kelas) continue;

            $dirty = false;

            if (isset($change['kompetensi_keahlian_id'])) {
                $kelas->kompetensi_keahlian_id = $change['kompetensi_keahlian_id'];
                $dirty = true;
            }

            if (array_key_exists('wali_user_id', $change)) {
                if ($change['wali_user_id']) {
                    KelasWali::updateOrCreate(
                        [
                            'kelas_id' => $kelas->id,
                            'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
                            'semester_id' => $sekolah?->semester_aktif,
                        ],
                        ['user_id' => $change['wali_user_id']]
                    );
                } else {
                    KelasWali::where('kelas_id', $kelas->id)
                        ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
                        ->where('semester_id', $sekolah?->semester_aktif)
                        ->delete();
                }
                $dirty = true;
            }

            if ($dirty) {
                $kelas->save();
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => $updated > 0
                ? "{$updated} rombel berhasil diperbarui."
                : 'Tidak ada perubahan yang disimpan.',
        ]);
    }

    public function updateJurusan(Request $request, Kelas $rombel)
    {
        $validated = $request->validate([
            'kompetensi_keahlian_id' => ['required', 'exists:kompetensi_keahlian,id'],
        ]);

        $rombel->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program keahlian berhasil diperbarui.',
        ]);
    }

    public function updateWali(Request $request, Kelas $rombel)
    {
        $validated = $request->validate([
            'wali_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $sekolah = Sekolah::first();

        if ($validated['wali_user_id']) {
            KelasWali::updateOrCreate(
                [
                    'kelas_id' => $rombel->id,
                    'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
                    'semester_id' => $sekolah?->semester_aktif,
                ],
                ['user_id' => $validated['wali_user_id']]
            );
        } else {
            KelasWali::where('kelas_id', $rombel->id)
                ->where('tahun_pelajaran_id', $sekolah?->tahun_aktif)
                ->where('semester_id', $sekolah?->semester_aktif)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Wali kelas berhasil diperbarui.',
        ]);
    }

    public function update(Request $request, Kelas $rombel)
    {
        $this->authorize('update', $rombel);

        $validated = $request->validate([
            'tingkat_id' => ['required', 'exists:tingkat,id'],
            'kompetensi_keahlian_id' => ['required', 'exists:kompetensi_keahlian,id'],
            'nama_kelas' => ['required', 'string', 'max:50'],
            'wali_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $rombel->update($validated);

        $sekolah = Sekolah::first();
        KelasWali::updateOrCreate(
            [
                'kelas_id' => $rombel->id,
                'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
                'semester_id' => $sekolah?->semester_aktif,
            ],
            ['user_id' => $validated['wali_user_id']]
        );

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
        $rombel->wali()->delete();
        $rombel->delete();

        activity()
            ->performedOn($rombel)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Rombel dihapus');

        return back()->with('status', 'Rombel berhasil dihapus.');
    }
}
