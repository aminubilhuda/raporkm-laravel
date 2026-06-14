<?php

namespace App\Services;

use App\Models\MapelKelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PenilaianService
{
    public function __construct(private SekolahService $sekolahService)
    {
    }
    /**
     * Batch store nilai records (formatif, sumatif ph, sumatif ts, sumatif as).
     *
     * @param  class-string<Model>  $model
     * @param  array<int, string>   $columns
     */
    public function batchStore(Request $request, string $model, array $columns, bool $tpKeyed = false): int
    {
        $data = $this->validate($request, $columns, $tpKeyed);
        $this->authorize($request->user(), $data['mapel_id'], $data['kelas_id']);

        $base = $this->buildBaseData($data);

        $saved = 0;

        foreach ($data['siswa_id'] as $siswaId) {
            $tpIds = $tpKeyed
                ? array_keys($request->input('nilai', []))
                : [null];

            foreach ($tpIds as $tpId) {
                $values = $this->buildNilaiValues($request, $columns, $tpKeyed, $siswaId, $tpId);

                if (empty($values)) {
                    continue;
                }

                $this->upsertRecord($model, $base, $values, $siswaId, $tpId, $tpKeyed);
                $saved++;
            }
        }

        return $saved;
    }

    /**
     * @return array<string, mixed>
     */
    private function validate(Request $request, array $columns, bool $tpKeyed): array
    {
        $rules = [
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'exists:siswa,id',
        ];

        foreach ($columns as $col) {
            $leafRule = $col === 'deskripsi'
                ? 'nullable|string'
                : 'nullable|integer|min:0|max:100';

            if ($tpKeyed) {
                // Form structure: col[tpId][siswaId] = value (2 levels deep)
                $rules["{$col}.*.*"] = $leafRule;
            } else {
                // Form structure: col[siswaId] = value (1 level deep)
                $rules["{$col}.*"] = $leafRule;
            }
        }

        return $request->validate($rules);
    }

    private function authorize($user, int $mapelId, int $kelasId): void
    {
        abort_unless(
            MapelKelas::where('user_id', $user->id)
                ->where('mapel_id', $mapelId)
                ->where('kelas_id', $kelasId)
                ->exists(),
            403
        );
    }

    /**
     * @return array<string, int>
     */
    private function buildBaseData(array $data): array
    {
        return [
            'tahun_pelajaran_id' => session('selected_tahun', $this->sekolahService->getTahunAktif()),
            'semester_id' => session('selected_semester', $this->sekolahService->getSemesterAktif()),
            'kelas_id' => $data['kelas_id'],
            'mapel_id' => $data['mapel_id'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildNilaiValues(Request $request, array $columns, bool $tpKeyed, int $siswaId, $tpId): array
    {
        $values = ['siswa_id' => $siswaId];

        if ($tpKeyed && $tpId !== null) {
            $values['tujuan_pembelajaran_id'] = (int) $tpId;
        }

        foreach ($columns as $col) {
            $payload = $request->input($col, []);

            if (! is_array($payload)) {
                continue;
            }

            if ($tpKeyed) {
                // Form structure: col[tpId][siswaId] = value
                $inner = $payload[(string) $tpId] ?? $payload[(int) $tpId] ?? null;
                if (is_array($inner)) {
                    $val = $inner[(string) $siswaId] ?? $inner[(int) $siswaId] ?? null;
                    if ($val !== null) {
                        $values[$col] = $val;
                    }
                }
            } else {
                // Form structure: col[siswaId] = value
                $val = $payload[(string) $siswaId] ?? $payload[(int) $siswaId] ?? null;
                if ($val !== null) {
                    $values[$col] = $val;
                }
            }
        }

        // Check if any actual nilai data was provided
        $hasValues = false;
        foreach ($columns as $col) {
            if (array_key_exists($col, $values)) {
                $hasValues = true;

                break;
            }
        }

        return $hasValues ? $values : [];
    }

    private function upsertRecord(string $model, array $base, array $values, int $siswaId, $tpId, bool $tpKeyed): void
    {
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
    }
}
