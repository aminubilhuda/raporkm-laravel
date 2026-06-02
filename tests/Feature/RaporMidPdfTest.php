<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\NilaiSumatifTs;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporMidPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_tu_dapat_download_rapor_mid_pdf(): void
    {
        $tu = User::factory()->tataUsaha()->create();
        $tahun = TahunPelajaran::factory()->create();
        $semester = Semester::factory()->create();
        Sekolah::factory()->create(['tahun_aktif' => $tahun->id, 'semester_aktif' => $semester->id]);

        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $kelas = Kelas::factory()->create(['tingkat_id' => $tingkat->id, 'kompetensi_keahlian_id' => $jurusan->id]);
        $mapel = Mapel::factory()->create(['kkm' => 75]);
        $siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $siswa->id, 'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id, 'semester_id' => $semester->id,
        ]);
        NilaiSumatifTs::factory()->create([
            'siswa_id' => $siswa->id, 'kelas_id' => $kelas->id, 'mapel_id' => $mapel->id,
            'tahun_pelajaran_id' => $tahun->id, 'semester_id' => $semester->id,
            'nilai' => 80,
        ]);

        $response = $this->actingAs($tu)->get(route('tu.rapor.mid', [
            'siswa' => $siswa->id, 'tahun' => $tahun->id, 'semester' => $semester->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
