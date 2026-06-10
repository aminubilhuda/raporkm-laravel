<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MapelKelas;
use App\Models\MapelSiswa;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MapelSiswaController extends Controller
{
    private const MAPEL_SHORT_MAP = [
        'Agama Islam' => 'PAIBP',
        'Agama Kristen' => 'PAKBP',
        'Agama Katolik' => 'PAKBP',
        'Agama Hindu' => 'PAHBP',
        'Agama Buddha' => 'PABBP',
        'Pancasila' => 'PP',
        'Bahasa Indonesia' => 'BI',
        'Bahasa Inggris' => 'BIG',
        'Matematika' => 'MTK',
        'Informatika' => 'INF',
        'Sejarah' => 'SEJ',
        'Projek IPAS' => 'IPAS',
        'IPAS' => 'IPAS',
        'Jasmani' => 'PJOK',
        'Konseling' => 'BK',
        'Akuntansi' => 'DDAKL',
        'Seni Budaya' => 'SBD',
        'Pemrograman Web' => 'PWP',
        'Basis Data' => 'BSD',
        'Jaringan' => 'JRK',
        'Sistem Operasi' => 'SO',
        'Pemrograman Berorientasi' => 'PBO',
        'Produk Kreatif' => 'PKK',
    ];

    public function index(Request $request)
    {
        $tpList = TahunPelajaran::orderByDesc('status')->orderByDesc('tahun')->get();
        $semesterList = Semester::orderBy('urutan')->get();
        $kelasList = Kelas::with('tingkat', 'kompetensiKeahlian')->orderBy('nama_kelas')->get();

        $sekolah = Sekolah::first();
        $selectedKelas = $request->input('kelas_id');
        $selectedTp = $request->input('tahun_pelajaran_id', session('selected_tahun', $sekolah?->tahun_aktif));
        $selectedSemester = $request->input('semester_id', session('selected_semester', $sekolah?->semester_aktif));

        $mapelList = collect();
        $siswaList = collect();
        $assignments = [];

        if ($selectedKelas && $selectedTp && $selectedSemester) {
            $mapelList = MapelKelas::with('mapel')
                ->where('kelas_id', $selectedKelas)
                ->where('tahun_pelajaran_id', $selectedTp)
                ->where('semester_id', $selectedSemester)
                ->get()
                ->sortBy(fn ($mk) => $mk->mapel?->urutan ?? 0);

            foreach ($mapelList as $mk) {
                $mk->short_name = $this->generateSingkatan(
                    $mk->mapel->nama_mapel ?? '',
                    $mk->mapel->kode ?? ''
                );
            }

            $siswaIds = SiswaKelas::where('kelas_id', $selectedKelas)
                ->where('tahun_pelajaran_id', $selectedTp)
                ->where('semester_id', $selectedSemester)
                ->where('status', 'aktif')
                ->pluck('siswa_id');

            $siswaList = Siswa::with('kompetensiKeahlian')
                ->whereIn('id', $siswaIds)
                ->orderBy('nama_siswa')
                ->get();

            $mapelKelasIds = $mapelList->pluck('id');

            $existingAssignments = MapelSiswa::whereIn('mapel_kelas_id', $mapelKelasIds)
                ->whereIn('siswa_id', $siswaIds)
                ->get();

            foreach ($existingAssignments as $a) {
                $assignments[$a->siswa_id][$a->mapel_kelas_id] = true;
            }
        }

        return view('tu.mapel-siswa.index', compact(
            'tpList', 'semesterList', 'kelasList',
            'mapelList', 'siswaList', 'assignments',
            'selectedKelas', 'selectedTp', 'selectedSemester'
        ));
    }

    public function batchUpdate(Request $request)
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
        ]);

        $kelasId = $request->integer('kelas_id');
        $tpId = $request->integer('tahun_pelajaran_id');
        $semesterId = $request->integer('semester_id');

        $mapelKelasIds = MapelKelas::where('kelas_id', $kelasId)
            ->where('tahun_pelajaran_id', $tpId)
            ->where('semester_id', $semesterId)
            ->pluck('id');

        $siswaIds = SiswaKelas::where('kelas_id', $kelasId)
            ->where('tahun_pelajaran_id', $tpId)
            ->where('semester_id', $semesterId)
            ->where('status', 'aktif')
            ->pluck('siswa_id');

        MapelSiswa::whereIn('mapel_kelas_id', $mapelKelasIds)
            ->whereIn('siswa_id', $siswaIds)
            ->forceDelete();

        $mapel = $request->input('mapel', []);
        $insertData = [];

        foreach ($mapel as $siswaId => $mapelKelasArr) {
            if (! is_array($mapelKelasArr)) {
                continue;
            }
            foreach ($mapelKelasArr as $mapelKelasId => $val) {
                if ($val == '1' || $val === 'on') {
                    $insertData[] = [
                        'siswa_id' => (int) $siswaId,
                        'mapel_kelas_id' => (int) $mapelKelasId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if ($insertData) {
            MapelSiswa::insert($insertData);
        }

        return back()->with('status', 'Peta mapel siswa berhasil disimpan. '.count($insertData).' penugasan diperbarui.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mapel_kelas_id' => ['required', 'exists:mapel_kelas,id'],
            'siswa_id' => ['required', 'exists:siswa,id'],
        ]);

        $exists = MapelSiswa::where('mapel_kelas_id', $validated['mapel_kelas_id'])
            ->where('siswa_id', $validated['siswa_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Siswa sudah terdaftar di mapel ini.');
        }

        MapelSiswa::create($validated);

        return back()->with('status', 'Siswa berhasil ditambahkan ke mapel.');
    }

    public function destroy($id)
    {
        MapelSiswa::findOrFail($id)->delete();

        return back()->with('status', 'Siswa berhasil dihapus dari mapel.');
    }

    private function generateSingkatan(string $namaMapel, string $kode): string
    {
        if ($kode !== '' && ! ctype_digit($kode)) {
            return strtoupper($kode);
        }

        foreach (self::MAPEL_SHORT_MAP as $needle => $short) {
            if (Str::contains($namaMapel, $needle, false)) {
                return $short;
            }
        }

        $words = preg_split('/[\s,\/]+/', $namaMapel);
        $initials = array_map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)), $words);

        return implode('', array_slice($initials, 0, 6));
    }
}
