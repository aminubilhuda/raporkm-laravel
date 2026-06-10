<?php

namespace Tests\Feature\Tu\Mutasi;

use App\Models\Kelas;
use App\Models\MutasiMasuk;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutasiMasukTest extends TestCase
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
        $this->tu = User::factory()->tataUsaha()->create();
        $this->guru = User::factory()->guru()->create();
    }

    public function test_tu_can_view_mutasi_masuk_index(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.mutasi-masuk.index'))
            ->assertStatus(200)
            ->assertSee('Mutasi');
    }

    public function test_tu_can_record_mutasi_masuk(): void
    {
        $siswa = Siswa::factory()->create();
        $kelas = Kelas::factory()->create();

        $this->actingAs($this->tu)->post(route('tu.mutasi-masuk.store'), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'asal_sekolah' => 'SMP Negeri 1',
            'tanggal_masuk' => '2026-01-15',
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('mutasi_masuk', [
            'siswa_id' => $siswa->id,
            'asal_sekolah' => 'SMP Negeri 1',
        ]);
    }

    public function test_create_mutasi_masuk_validates_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.mutasi-masuk.store'), ['keterangan' => 'kosong'])
            ->assertSessionHasErrors(['siswa_id', 'kelas_id', 'asal_sekolah', 'tanggal_masuk']);
    }

    public function test_tu_can_delete_mutasi_masuk(): void
    {
        $m = MutasiMasuk::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.mutasi-masuk.destroy', $m))
            ->assertRedirect();

        $this->assertDatabaseHas('mutasi_masuk', ['id' => $m->id]);
        $this->assertNotNull($m->fresh()->deleted_at);
    }

    public function test_guru_cannot_access_mutasi_masuk(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.mutasi-masuk.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
