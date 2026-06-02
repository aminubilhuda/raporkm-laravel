<?php

namespace Tests\Feature\Tu\Lulusan;

use App\Models\Kelas;
use App\Models\Lulusan;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LulusanTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private User $guru;

    private TahunPelajaran $tahun;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tahun = TahunPelajaran::factory()->create();
        $this->tu = User::factory()->tataUsaha()->create();
        $this->guru = User::factory()->guru()->create();
    }

    public function test_tu_can_view_lulusan_index(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.lulusan.index'))
            ->assertStatus(200)
            ->assertSee('Kelulusan');
    }

    public function test_tu_can_record_lulusan(): void
    {
        $siswa = Siswa::factory()->create();
        $kelas = Kelas::factory()->create();

        $this->actingAs($this->tu)->post(route('tu.lulusan.store'), [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'tanggal_lulus' => '2026-06-15',
            'no_ijazah' => 'IJZ-001',
            'lanjut_ke' => 'Kuliah',
        ])->assertRedirect();

        $this->assertDatabaseHas('lulusan', [
            'siswa_id' => $siswa->id,
            'no_ijazah' => 'IJZ-001',
        ]);
    }

    public function test_create_lulusan_validates_siswa_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.lulusan.store'), ['no_ijazah' => 'tanpa siswa'])
            ->assertSessionHasErrors(['siswa_id', 'kelas_id']);
    }

    public function test_tu_can_update_lulusan(): void
    {
        $l = Lulusan::factory()->create(['lanjut_ke' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.lulusan.update', $l), [
            'lanjut_ke' => 'Kerja',
        ])->assertRedirect();

        $this->assertDatabaseHas('lulusan', ['id' => $l->id, 'lanjut_ke' => 'Kerja']);
    }

    public function test_tu_can_delete_lulusan(): void
    {
        $l = Lulusan::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.lulusan.destroy', $l))
            ->assertRedirect();

        $this->assertDatabaseMissing('lulusan', ['id' => $l->id]);
    }

    public function test_guru_cannot_access_tu_lulusan(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.lulusan.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
