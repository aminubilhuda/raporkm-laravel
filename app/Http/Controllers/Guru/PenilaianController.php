<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiFormatif;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\NilaiSumatifTs;
use App\Models\Sekolah;
use App\Models\SiswaKelas;
use App\Models\TujuanPembelajaran;
use Illuminate\Database\Eloquent\Model;

class PenilaianController extends Controller
{
    public function index(?Kelas $kelas = null, ?Mapel $mapel = null)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;

        $mapelKelasList = $user->mapelKelas()
            ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->with('mapel', 'kelas.tingkat', 'kelas.kompetensiKeahlian')
            ->get();

        $authorized = $kelas && $mapel && $mapelKelasList->contains(fn ($mk) => $mk->kelas_id === $kelas->id && $mk->mapel_id === $mapel->id);

        $siswa = collect();
        $tujuanPembelajaran = collect();
        $nilaiFormatif = collect();
        $nilaiSumatifPh = collect();
        $nilaiSumatifTs = collect();
        $nilaiSumatifAs = collect();

        if ($authorized) {
            $siswa = SiswaKelas::where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->with('siswa')
                ->get();

            $tujuanPembelajaran = TujuanPembelajaran::where('mapel_id', $mapel->id)
                ->where('kelas_id', $kelas->id)
                ->when($taId, fn ($q) => $q->where('tahun_pelajaran_id', $taId))
                ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
                ->get();

            $siswaIds = $siswa->pluck('siswa_id');
            $tpIds = $tujuanPembelajaran->pluck('id');

            $nilaiFormatif = NilaiFormatif::whereIn('siswa_id', $siswaIds)
                ->whereIn('tujuan_pembelajaran_id', $tpIds)
                ->where('mapel_id', $mapel->id)
                ->where('kelas_id', $kelas->id)
                ->get()
                ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->tujuan_pembelajaran_id}");

            $nilaiSumatifPh = NilaiSumatifPh::whereIn('siswa_id', $siswaIds)
                ->whereIn('tujuan_pembelajaran_id', $tpIds)
                ->where('mapel_id', $mapel->id)
                ->where('kelas_id', $kelas->id)
                ->get()
                ->keyBy(fn ($n) => "{$n->siswa_id}_{$n->tujuan_pembelajaran_id}");

            $nilaiSumatifTs = NilaiSumatifTs::whereIn('siswa_id', $siswaIds)
                ->where('mapel_id', $mapel->id)
                ->where('kelas_id', $kelas->id)
                ->get()
                ->keyBy(fn ($n) => "{$n->siswa_id}");

            $nilaiSumatifAs = NilaiSumatifAs::whereIn('siswa_id', $siswaIds)
                ->where('mapel_id', $mapel->id)
                ->where('kelas_id', $kelas->id)
                ->get()
                ->keyBy(fn ($n) => "{$n->siswa_id}");
        }

        return view('guru.penilaian.index', compact(
            'mapelKelasList', 'kelas', 'mapel', 'authorized',
            'siswa', 'tujuanPembelajaran', 'nilaiFormatif', 'nilaiSumatifPh',
            'nilaiSumatifTs', 'nilaiSumatifAs'
        ));
    }

    public function storeFormatif()
    {
        return $this->batchStore(NilaiFormatif::class, ['nilai', 'middle', 'nas'], tpKeyed: true);
    }

    public function storeSumatifPh()
    {
        return $this->batchStore(NilaiSumatifPh::class, ['nilai', 'deskripsi'], tpKeyed: true);
    }

    public function storeSumatifTs()
    {
        return $this->batchStore(NilaiSumatifTs::class, ['nilai', 'deskripsi']);
    }

    public function storeSumatifAs()
    {
        return $this->batchStore(NilaiSumatifAs::class, ['nilai', 'deskripsi']);
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<int, string>  $columns
     */
    private function batchStore(string $model, array $columns, bool $tpKeyed = false)
    {
        $user = auth()->user();
        $sekolah = Sekolah::first();

        $rules = [
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'exists:siswa,id',
        ];

        foreach ($columns as $col) {
            $rules["{$col}.*"] = $col === 'deskripsi'
                ? 'nullable|string'
                : 'nullable|integer|min:0|max:100';
        }

        $data = request()->validate($rules);

        abort_unless(
            $user->mapelKelas()->where('mapel_id', $data['mapel_id'])->where('kelas_id', $data['kelas_id'])->exists(),
            403
        );

        $base = [
            'tahun_pelajaran_id' => $sekolah?->tahun_aktif,
            'semester_id' => $sekolah?->semester_aktif,
            'kelas_id' => $data['kelas_id'],
            'mapel_id' => $data['mapel_id'],
        ];

        $saved = 0;
        foreach ($data['siswa_id'] as $siswaId) {
            $tpIds = $tpKeyed
                ? array_keys(request()->input('nilai', []))
                : [null];

            foreach ($tpIds as $tpId) {
                $values = ['siswa_id' => $siswaId];
                if ($tpKeyed && $tpId !== null) {
                    $values['tujuan_pembelajaran_id'] = (int) $tpId;
                }

                $hasValue = false;
                foreach ($columns as $col) {
                    $key = $tpKeyed ? $tpId : $siswaId;
                    $payload = request()->input($col, []);
                    if (! is_array($payload)) {
                        continue;
                    }
                    if (array_key_exists((string) $key, $payload)) {
                        $values[$col] = $payload[(string) $key];
                        $hasValue = true;
                    } elseif (array_key_exists((int) $key, $payload)) {
                        $values[$col] = $payload[(int) $key];
                        $hasValue = true;
                    }
                }

                if (! $hasValue) {
                    continue;
                }

                $lookup = array_merge($base, ['siswa_id' => $siswaId]);
                if ($tpKeyed && $tpId !== null) {
                    $lookup['tujuan_pembelajaran_id'] = (int) $tpId;
                }

                $record = $model::where($lookup)->first();
                if ($record) {
                    $record->update($values);
                } else {
                    $model::create(array_merge($base, $values));
                }
                $saved++;
            }
        }

        return redirect()->route('guru.penilaian.index', ['kelas' => $data['kelas_id'], 'mapel' => $data['mapel_id']])
            ->with('status', "{$saved} nilai berhasil disimpan.");
    }
}
