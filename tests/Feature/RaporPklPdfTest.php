<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\NilaiPrakerin;
use App\Models\Prakerin;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporPklPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_tu_dapat_download_rapor_pkl_pdf(): void
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
        $prakerin = Prakerin::factory()->create([
            'tahun_pelajaran_id' => $tahun->id, 'semester_id' => $semester->id,
        ]);
        $siswaPrakerin = SiswaPrakerin::factory()->create([
            'siswa_id' => $siswa->id, 'prakerin_id' => $prakerin->id, 'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id, 'semester_id' => $semester->id,
        ]);
        NilaiPrakerin::factory()->create([
            'siswa_prakerin_id' => $siswaPrakerin->id, 'mapel_id' => $mapel->id, 'nilai' => 85,
        ]);

        $response = $this->actingAs($tu)->get(route('tu.rapor.pkl', $siswaPrakerin->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
