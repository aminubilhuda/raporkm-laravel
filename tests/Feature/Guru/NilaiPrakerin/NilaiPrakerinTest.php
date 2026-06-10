<?php

namespace Tests\Feature\Guru\NilaiPrakerin;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\NilaiPrakerin;
use App\Models\Prakerin;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NilaiPrakerinTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private User $guruLain;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Kelas $kelas;

    private Prakerin $prakerin;

    private SiswaPrakerin $siswaPrakerin;

    private Mapel $mapel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();

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

        $this->guru = User::factory()->guru()->create();
        $this->guruLain = User::factory()->guru()->create();

        $this->prakerin = Prakerin::factory()->create([
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $siswa = Siswa::factory()->create();
        $this->siswaPrakerin = SiswaPrakerin::factory()->create([
            'prakerin_id' => $this->prakerin->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'status' => 'aktif',
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->mapel = Mapel::factory()->create();
        MapelKelas::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_guru_bisa_lihat_daftar_siswa_bimbingan(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.nilai-prakerin.index'))
            ->assertOk()
            ->assertViewIs('guru.nilai-prakerin.index')
            ->assertSee($this->siswaPrakerin->siswa->nama_siswa);
    }

    public function test_guru_bisa_lihat_form_edit_nilai_prakerin(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.nilai-prakerin.edit', $this->siswaPrakerin))
            ->assertOk()
            ->assertViewIs('guru.nilai-prakerin.edit')
            ->assertSee($this->mapel->nama_mapel);
    }

    public function test_guru_lain_tidak_bisa_edit_nilai_prakerin(): void
    {
        $this->actingAs($this->guruLain)
            ->get(route('guru.nilai-prakerin.edit', $this->siswaPrakerin))
            ->assertForbidden();
    }

    public function test_guru_bisa_simpan_nilai_prakerin(): void
    {
        $this->actingAs($this->guru)->post(route('guru.nilai-prakerin.store', $this->siswaPrakerin), [
            'mapel_id' => [$this->mapel->id],
            'nilai' => [85],
            'deskripsi' => ['Baik'],
        ]);

        $this->assertDatabaseHas('nilai_prakerin', [
            'siswa_prakerin_id' => $this->siswaPrakerin->id,
            'mapel_id' => $this->mapel->id,
            'nilai' => 85,
            'deskripsi' => 'Baik',
        ]);
    }

    public function test_guru_lain_tidak_bisa_simpan_nilai_prakerin(): void
    {
        $this->actingAs($this->guruLain)->post(route('guru.nilai-prakerin.store', $this->siswaPrakerin), [
            'mapel_id' => [$this->mapel->id],
            'nilai' => [85],
        ])->assertForbidden();
    }

    public function test_nilai_prakerin_wajib_diantara_0_dan_100(): void
    {
        $this->actingAs($this->guru)->post(route('guru.nilai-prakerin.store', $this->siswaPrakerin), [
            'mapel_id' => [$this->mapel->id],
            'nilai' => [150],
        ])->assertSessionHasErrors('nilai.0');
    }

    public function test_guru_bisa_update_nilai_prakerin(): void
    {
        NilaiPrakerin::factory()->create([
            'siswa_prakerin_id' => $this->siswaPrakerin->id,
            'mapel_id' => $this->mapel->id,
            'nilai' => 70,
        ]);

        $this->actingAs($this->guru)->post(route('guru.nilai-prakerin.store', $this->siswaPrakerin), [
            'mapel_id' => [$this->mapel->id],
            'nilai' => [90],
            'deskripsi' => ['Sangat Baik'],
        ]);

        $this->assertDatabaseHas('nilai_prakerin', [
            'siswa_prakerin_id' => $this->siswaPrakerin->id,
            'mapel_id' => $this->mapel->id,
            'nilai' => 90,
            'deskripsi' => 'Sangat Baik',
        ]);
    }

    public function test_siswa_tidak_aktif_tidak_bisa_diinput_nilai(): void
    {
        $this->siswaPrakerin->update(['status' => 'selesai']);

        $this->actingAs($this->guru)
            ->get(route('guru.nilai-prakerin.edit', $this->siswaPrakerin))
            ->assertSee('tidak aktif');
    }
}
