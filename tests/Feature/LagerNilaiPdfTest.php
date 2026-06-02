<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\NilaiMapel;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LagerNilaiPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_dapat_download_lager_nilai_pdf(): void
    {
        $guru = User::factory()->guru()->create();
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
        MapelKelas::factory()->create([
            'kelas_id' => $kelas->id, 'mapel_id' => $mapel->id, 'user_id' => $guru->id,
            'tahun_pelajaran_id' => $tahun->id, 'semester_id' => $semester->id,
        ]);
        NilaiMapel::factory()->create([
            'siswa_id' => $siswa->id, 'kelas_id' => $kelas->id, 'mapel_id' => $mapel->id,
            'tahun_pelajaran_id' => $tahun->id, 'semester_id' => $semester->id,
            'nilai' => 85, 'predikat' => 'B',
        ]);

        $response = $this->actingAs($guru)->get(route('guru.lager-nilai-kelas.pdf', [
            'kelas' => $kelas->id, 'mapel' => $mapel->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
