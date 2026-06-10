<?php

namespace Tests\Feature\Guru\PenilaianProfilPancasila;

use App\Models\Dimensi;
use App\Models\Elemen;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\SubElemen;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenilaianProfilPancasilaTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private User $guruLain;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Kelas $kelas;

    private ProyekKelas $proyekKelas;

    private Siswa $siswa;

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
        $this->siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        // Create dimensi, elemen, sub-elemen
        $dimensi = Dimensi::factory()->create(['urutan' => 1]);
        $elemen = Elemen::factory()->create(['dimensi_id' => $dimensi->id, 'urutan' => 1]);
        $subElemen = SubElemen::factory()->create(['elemen_id' => $elemen->id, 'urutan' => 1]);

        // Create tema and proyek
        $tema = ProyekTema::factory()->create([
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->proyekKelas = ProyekKelas::factory()->create([
            'kelas_id' => $this->kelas->id,
            'proyek_tema_id' => $tema->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_wali_kelas_bisa_lihat_index(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.penilaian-profil-pancasila.index', $this->kelas))
            ->assertOk()
            ->assertViewIs('guru.penilaian-profil-pancasila.index')
            ->assertViewHas('authorized', true);
    }

    public function test_bukan_wali_tidak_bisa_lihat_kelas(): void
    {
        $this->actingAs($this->guruLain)
            ->get(route('guru.penilaian-profil-pancasila.index', $this->kelas))
            ->assertOk()
            ->assertViewHas('authorized', false);
    }

    public function test_wali_kelas_bisa_lihat_daftar_proyek(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.penilaian-profil-pancasila.index', $this->kelas))
            ->assertOk()
            ->assertSee($this->proyekKelas->judul);
    }

    public function test_wali_kelas_bisa_buka_form_penilaian(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.penilaian-profil-pancasila.penilaian', $this->proyekKelas))
            ->assertOk()
            ->assertViewIs('guru.penilaian-profil-pancasila.penilaian')
            ->assertSee($this->proyekKelas->judul);
    }

    public function test_bukan_wali_tidak_bisa_buka_form_penilaian(): void
    {
        $this->actingAs($this->guruLain)
            ->get(route('guru.penilaian-profil-pancasila.penilaian', $this->proyekKelas))
            ->assertForbidden();
    }

    public function test_wali_bisa_simpan_nilai(): void
    {
        $subElemen = SubElemen::first();

        $response = $this->actingAs($this->guru)->post(
            route('guru.penilaian-profil-pancasila.store', $this->proyekKelas),
            [
                'siswa_id' => [$this->siswa->id],
                'nilai' => [
                    $this->siswa->id => [
                        $subElemen->id => 85,
                    ],
                ],
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Nilai Profil Pancasila berhasil disimpan.');

        $this->assertDatabaseHas('nilai_assesmen_subelemen', [
            'proyek_kelas_id' => $this->proyekKelas->id,
            'sub_elemen_id' => $subElemen->id,
            'siswa_id' => $this->siswa->id,
            'nilai' => 85,
        ]);
    }

    public function test_nilai_diluar_batas_100_gagal(): void
    {
        $subElemen = SubElemen::first();

        $response = $this->actingAs($this->guru)->post(
            route('guru.penilaian-profil-pancasila.store', $this->proyekKelas),
            [
                'siswa_id' => [$this->siswa->id],
                'nilai' => [
                    $this->siswa->id => [
                        $subElemen->id => 150,
                    ],
                ],
            ]
        );

        $response->assertSessionHasErrors('nilai.*.*');
    }

    public function test_bukan_wali_tidak_bisa_simpan_nilai(): void
    {
        $subElemen = SubElemen::first();

        $response = $this->actingAs($this->guruLain)->post(
            route('guru.penilaian-profil-pancasila.store', $this->proyekKelas),
            [
                'siswa_id' => [$this->siswa->id],
                'nilai' => [
                    $this->siswa->id => [
                        $subElemen->id => 85,
                    ],
                ],
            ]
        );

        $response->assertForbidden();
    }
}
