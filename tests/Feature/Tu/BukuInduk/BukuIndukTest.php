<?php

namespace Tests\Feature\Tu\BukuInduk;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BukuIndukTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tu = User::factory()->tataUsaha()->create();
        $this->guru = User::factory()->guru()->create();
    }

    public function test_tu_can_view_buku_induk(): void
    {
        Siswa::factory()->count(3)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.buku-induk.index'))
            ->assertStatus(200)
            ->assertSee('Buku Induk Siswa');
    }

    public function test_guru_cannot_access_buku_induk(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.buku-induk.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function test_buku_induk_filter_by_kelas(): void
    {
        $tingkat = Tingkat::factory()->create(['angka' => 10, 'nama' => '10', 'fase' => 'E', 'urutan' => 1]);
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $kelas = Kelas::create(['tingkat_id' => $tingkat->id, 'kompetensi_keahlian_id' => $kk->id, 'nama_kelas' => 'X TKJ 1']);
        $tp = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => 1]);
        $semester = \DB::table('semester')->insertGetId(['nama' => 'Ganjil', 'urutan' => 1, 'status' => 1]);

        $siswaA = Siswa::factory()->create(['nama_siswa' => 'Budi Santoso']);
        SiswaKelas::create([
            'siswa_id' => $siswaA->id, 'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tp->id, 'semester_id' => $semester,
            'status' => 'aktif',
        ]);

        $siswaB = Siswa::factory()->create(['nama_siswa' => 'Siti Rahayu']);

        $this->actingAs($this->tu)
            ->get(route('tu.buku-induk.index', ['kelas_id' => $kelas->id]))
            ->assertStatus(200)
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Rahayu');
    }

    public function test_buku_induk_search(): void
    {
        Siswa::factory()->create(['nama_siswa' => 'Ahmad Fauzi', 'nisn' => '0012345678']);
        Siswa::factory()->create(['nama_siswa' => 'Dewi Lestari', 'nisn' => '0098765432']);

        $this->actingAs($this->tu)
            ->get(route('tu.buku-induk.index', ['search' => 'Ahmad']))
            ->assertStatus(200)
            ->assertSee('Ahmad Fauzi')
            ->assertDontSee('Dewi Lestari');

        $this->actingAs($this->tu)
            ->get(route('tu.buku-induk.index', ['search' => '001234']))
            ->assertStatus(200)
            ->assertSee('Ahmad Fauzi');
    }

    public function test_buku_induk_pdf_download(): void
    {
        Sekolah::create([
            'npsn' => '20501001',
            'nama_sekolah' => 'SMK Negeri 1 Maju Bersama',
            'alamat' => 'Jl. Pendidikan No. 1',
        ]);

        Siswa::factory()->count(2)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.buku-induk.pdf'))
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
