<?php

namespace Tests\Feature\Guru\Presensi;

use App\Models\JenisAbsen;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private Kelas $kelas;

    private Siswa $siswa;

    private TahunPelajaran $tahun;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();
        $this->guru = User::factory()->guru()->create();

        Sekolah::factory()->create([
            'tahun_aktif' => $this->tahun->id,
            'semester_aktif' => $this->semester->id,
        ]);

        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $this->kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);

        $this->siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        JenisAbsen::insert([
            ['id' => 1, 'nama' => 'Hadir'],
            ['id' => 2, 'nama' => 'Sakit'],
            ['id' => 3, 'nama' => 'Izin'],
            ['id' => 4, 'nama' => 'Alpa'],
        ]);
    }

    public function test_guru_can_view_presensi_form(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.presensi.index'))
            ->assertStatus(200)
            ->assertSee('Presensi Harian');
    }

    public function test_guru_can_store_presensi(): void
    {
        $this->actingAs($this->guru)->post(route('guru.presensi.store'), [
            'kelas_id' => $this->kelas->id,
            'tanggal' => now()->format('Y-m-d'),
            'siswa_id' => [$this->siswa->id],
            'jenis_absen_id' => [$this->siswa->id => 1],
        ])->assertRedirect();

        $this->assertDatabaseHas('presensi', [
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'jenis_absen_id' => 1,
        ]);
    }

    public function test_guru_can_view_rekap_presensi(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.presensi.rekap'))
            ->assertStatus(200)
            ->assertSee('Rekap Presensi');
    }

    public function test_presensi_validates_required_fields(): void
    {
        $this->actingAs($this->guru)
            ->post(route('guru.presensi.store'), [])
            ->assertSessionHasErrors(['kelas_id', 'tanggal', 'siswa_id']);
    }
}
