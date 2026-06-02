<?php

namespace Tests\Feature\Laporan;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\NilaiMapel;
use App\Models\Presensi;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendidikanTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Sekolah $sekolah;

    private Kelas $kelas;

    private Mapel $mapel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRefData();

        $this->tu = User::factory()->tataUsaha()->create();
        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();
        $this->sekolah = Sekolah::factory()->create([
            'tahun_aktif' => $this->tahun->id,
            'semester_aktif' => $this->semester->id,
        ]);
        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $this->kelas = Kelas::factory()->create(['tingkat_id' => $tingkat->id, 'kompetensi_keahlian_id' => $jurusan->id]);
        $this->mapel = Mapel::factory()->create();

        foreach (['Ahmad', 'Budi', 'Citra'] as $i => $nama) {
            $siswa = Siswa::factory()->create(['nama_siswa' => $nama]);
            SiswaKelas::factory()->create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $this->kelas->id,
                'tahun_pelajaran_id' => $this->tahun->id,
                'semester_id' => $this->semester->id,
                'status' => 'aktif',
            ]);
            NilaiMapel::factory()->create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $this->kelas->id,
                'mapel_id' => $this->mapel->id,
                'tahun_pelajaran_id' => $this->tahun->id,
                'semester_id' => $this->semester->id,
                'nilai' => 80 + $i,
                'predikat' => ['A', 'B', 'C'][$i],
            ]);
        }

        $sakitSiswa = Siswa::where('nama_siswa', 'Ahmad')->first();
        Presensi::create([
            'siswa_id' => $sakitSiswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
            'jenis_absen_id' => 2,
            'tanggal' => '2026-01-10',
        ]);
    }

    public function test_tu_can_view_laporan_pendidikan(): void
    {
        $response = $this->actingAs($this->tu)->get(route('tu.laporan.pendidikan'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Pendidikan');
        $response->assertSee('Rata-rata Nilai per Mapel');
        $response->assertSee('Distribusi Predikat');
        $response->assertSee('Top 10 Siswa');
        $response->assertSee('Bottom 10 Siswa');
        $response->assertSee('Ahmad');
        $response->assertSee($this->mapel->nama_mapel);
    }

    public function test_laporan_with_explicit_filter_params(): void
    {
        $response = $this->actingAs($this->tu)->get(route('tu.laporan.pendidikan', [
            'tahun' => $this->tahun->id,
            'semester' => $this->semester->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->mapel->nama_mapel);
    }

    public function test_unauthenticated_user_redirected(): void
    {
        $response = $this->get(route('tu.laporan.pendidikan'));
        $response->assertRedirect();
    }

    private function seedRefData(): void
    {
        \DB::table('jenis_absen')->insert([
            ['id' => 1, 'nama' => 'Hadir'],
            ['id' => 2, 'nama' => 'Sakit'],
            ['id' => 3, 'nama' => 'Izin'],
            ['id' => 4, 'nama' => 'Tanpa Keterangan'],
        ]);
    }
}
