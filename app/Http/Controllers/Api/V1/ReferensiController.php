<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RefResource;
use App\Models\DeskripsiRapor;
use App\Models\Dimensi;
use App\Models\DimensiKokurikuler;
use App\Models\Elemen;
use App\Models\Eskul;
use App\Models\JenisAbsen;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\RefAgama;
use App\Models\RefBulan;
use App\Models\RefHari;
use App\Models\RefHubunganKeluarga;
use App\Models\RefJabatan;
use App\Models\RefJenisKelamin;
use App\Models\RefJenisKeluar;
use App\Models\RefJenisSiswa;
use App\Models\RefKepegawaian;
use App\Models\RefKurikulum;
use App\Models\RefPendidikan;
use App\Models\RefTugasTambahan;
use App\Models\Semester;
use App\Models\SubElemen;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferensiController extends Controller
{
    private static array $registry = [
        'agama' => RefAgama::class,
        'jenis-kelamin' => RefJenisKelamin::class,
        'hubungan-keluarga' => RefHubunganKeluarga::class,
        'jabatan' => RefJabatan::class,
        'kepegawaian' => RefKepegawaian::class,
        'pendidikan' => RefPendidikan::class,
        'tugas-tambahan' => RefTugasTambahan::class,
        'jenis-siswa' => RefJenisSiswa::class,
        'jenis-keluar' => RefJenisKeluar::class,
        'kurikulum' => RefKurikulum::class,
        'jenis-absen' => JenisAbsen::class,
        'kelompok-mapel' => KelompokMapel::class,
        'tingkat' => Tingkat::class,
        'kompetensi' => KompetensiKeahlian::class,
        'tahun-pelajaran' => TahunPelajaran::class,
        'semester' => Semester::class,
        'bulan' => RefBulan::class,
        'hari' => RefHari::class,
        'dimensi' => Dimensi::class,
        'dimensi-kokurikuler' => DimensiKokurikuler::class,
        'elemen' => Elemen::class,
        'eskul' => Eskul::class,
        'mapel' => Mapel::class,
        'deskripsi-rapor' => DeskripsiRapor::class,
        'sub-elemen' => SubElemen::class,
    ];

    private static array $ordered = [
        'tingkat' => 'angka',
        'semester' => 'urutan',
        'tahun-pelajaran' => 'tahun',
        'bulan' => 'urutan',
        'hari' => 'urutan',
        'dimensi' => 'urutan',
        'mapel' => 'urutan',
    ];

    private static array $descending = [
        'tahun-pelajaran' => 'tahun',
    ];

    public function index(Request $request): JsonResponse
    {
        $available = collect(self::$registry)->keys();

        return response()->json([
            'success' => true,
            'data' => $available,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        if (! isset(self::$registry[$slug])) {
            return response()->json([
                'success' => false,
                'message' => "Referensi '{$slug}' tidak ditemukan.",
            ], 404);
        }

        $model = self::$registry[$slug];

        $query = $model::query();

        if (isset(self::$descending[$slug])) {
            $query->orderByDesc(self::$descending[$slug]);
        } elseif (isset(self::$ordered[$slug])) {
            $query->orderBy(self::$ordered[$slug]);
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => RefResource::collection($data),
        ]);
    }

    public function dimensiWithElemens(): JsonResponse
    {
        $data = Dimensi::with('elemens.subElemens')->orderBy('urutan')->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
