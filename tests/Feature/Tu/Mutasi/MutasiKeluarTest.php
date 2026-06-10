<?php

namespace Tests\Feature\Tu\Mutasi;

use App\Models\Kelas;
use App\Models\MutasiKeluar;
use App\Models\RefJenisKeluar;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutasiKeluarTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private User $guru;

    private TahunPelajaran $tahun;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();
        RefJenisKeluar::factory()->create(['nama' => 'Pindah']);
        $this->tu = User::factory()->tataUsaha()->create();
        $this->guru = User::factory()->guru()->create();
    }

    public function test_tu_can_view_mutasi_keluar_index(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.mutasi-keluar.index'))
            ->assertStatus(200)
            ->assertSee('Mutasi');
    }

    public function test_tu_can_record_mutasi_keluar(): void
    {
        $siswa = Siswa::factory()->create();
        $kelas = Kelas::factory()->create();
        $jenis = RefJenisKeluar::first();

        $this->actingAs($this->tu)->post(route('tu.mutasi-keluar.store'), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tujuan_sekolah' => 'SMA Negeri 1',
            'tanggal_keluar' => '2026-01-15',
            'jenis_keluar_id' => $jenis->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('mutasi_keluar', [
            'siswa_id' => $siswa->id,
            'tujuan_sekolah' => 'SMA Negeri 1',
        ]);
    }

    public function test_create_mutasi_keluar_validates_tanggal_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.mutasi-keluar.store'), ['tujuan_sekolah' => 'tanpa tanggal'])
            ->assertSessionHasErrors(['siswa_id', 'kelas_id', 'tanggal_keluar']);
    }

    public function test_tu_can_delete_mutasi_keluar(): void
    {
        $m = MutasiKeluar::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.mutasi-keluar.destroy', $m))
            ->assertRedirect();

        $this->assertDatabaseHas('mutasi_keluar', ['id' => $m->id]);
        $this->assertNotNull($m->fresh()->deleted_at);
    }

    public function test_guru_cannot_access_mutasi_keluar(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.mutasi-keluar.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
