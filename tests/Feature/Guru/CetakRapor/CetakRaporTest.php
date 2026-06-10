<?php

namespace Tests\Feature\Guru\CetakRapor;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CetakRaporTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private User $guruLain;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Kelas $kelas;

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

        // Make guru as wali kelas
        \DB::table('kelas_wali')->insert([
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        // Create siswa in kelas
        $siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        // Create mapel for kelas
        $mapel = Mapel::factory()->create();
        MapelKelas::factory()->create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_wali_kelas_bisa_lihat_cetak_rapor_index(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.cetak-rapor.index', $this->kelas))
            ->assertOk()
            ->assertViewIs('guru.cetak-rapor.index')
            ->assertViewHas('authorized', true);
    }

    public function test_bukan_wali_tidak_bisa_lihat_cetak_rapor_kelas(): void
    {
        $this->actingAs($this->guruLain)
            ->get(route('guru.cetak-rapor.index', $this->kelas))
            ->assertOk()
            ->assertViewHas('authorized', false);
    }

    public function test_wali_kelas_bisa_lihat_daftar_siswa(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.cetak-rapor.index', $this->kelas))
            ->assertOk()
            ->assertSee('Daftar Siswa');
    }

    public function test_guru_tidak_bisa_cetak_rapor_kelas_bukan_walianya(): void
    {
        $siswa = Siswa::first();
        $response = $this->actingAs($this->guruLain)->post(route('guru.cetak-rapor.cetak', $this->kelas), [
            'siswa_id' => [$siswa->id],
            'jenis' => 'semester',
        ]);

        $response->assertForbidden();
    }

    public function test_guru_bisa_cetak_rapor_single_siswa(): void
    {
        $siswa = Siswa::first();
        $response = $this->actingAs($this->guru)->post(route('guru.cetak-rapor.cetak', $this->kelas), [
            'siswa_id' => [$siswa->id],
            'jenis' => 'semester',
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_guru_bisa_cetak_rapor_mid_siswa(): void
    {
        $siswa = Siswa::first();
        $response = $this->actingAs($this->guru)->post(route('guru.cetak-rapor.cetak', $this->kelas), [
            'siswa_id' => [$siswa->id],
            'jenis' => 'mid',
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_cetak_rapor_batch_siswa_menghasilkan_zip(): void
    {
        // Add a second student to ensure batch mode is triggered
        $siswa2 = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $siswa2->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $allSiswa = SiswaKelas::where('kelas_id', $this->kelas->id)
            ->where('tahun_pelajaran_id', $this->tahun->id)
            ->pluck('siswa_id')
            ->toArray();

        $response = $this->actingAs($this->guru)->post(route('guru.cetak-rapor.cetak', $this->kelas), [
            'siswa_id' => $allSiswa,
            'jenis' => 'semester',
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/zip');
    }

    public function test_cetak_rapor_jenis_wajib_dipilih(): void
    {
        $siswa = Siswa::first();
        $response = $this->actingAs($this->guru)->post(route('guru.cetak-rapor.cetak', $this->kelas), [
            'siswa_id' => [$siswa->id],
        ]);

        $response->assertSessionHasErrors('jenis');
    }
}
