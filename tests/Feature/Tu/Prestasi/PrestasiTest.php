<?php

namespace Tests\Feature\Tu\Prestasi;

use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrestasiTest extends TestCase
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

    public function test_tu_can_view_prestasi_index(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.prestasi.index'))
            ->assertStatus(200)
            ->assertSee('Prestasi');
    }

    public function test_tu_can_record_prestasi(): void
    {
        $siswa = Siswa::factory()->create();

        $this->actingAs($this->tu)->post(route('tu.prestasi.store'), [
            'siswa_id' => $siswa->id,
            'nama_prestasi' => 'Juara 1 Lomba Robotik',
            'tingkat' => 'Nasional',
            'penyelenggara' => 'Kemendikbud',
            'tahun' => 2026,
        ])->assertRedirect();

        $this->assertDatabaseHas('prestasi', [
            'siswa_id' => $siswa->id,
            'nama_prestasi' => 'Juara 1 Lomba Robotik',
        ]);
    }

    public function test_create_prestasi_validates_nama_required(): void
    {
        $siswa = Siswa::factory()->create();

        $this->actingAs($this->tu)
            ->post(route('tu.prestasi.store'), ['siswa_id' => $siswa->id, 'tingkat' => 'Lokal'])
            ->assertSessionHasErrors('nama_prestasi');
    }

    public function test_tu_can_update_prestasi(): void
    {
        $p = Prestasi::factory()->create(['nama_prestasi' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.prestasi.update', $p), [
            'nama_prestasi' => 'Baru',
        ])->assertRedirect();

        $this->assertDatabaseHas('prestasi', ['id' => $p->id, 'nama_prestasi' => 'Baru']);
    }

    public function test_tu_can_delete_prestasi(): void
    {
        $p = Prestasi::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.prestasi.destroy', $p))
            ->assertRedirect();

        $this->assertSoftDeleted('prestasi', ['id' => $p->id]);
    }

    public function test_guru_cannot_access_tu_prestasi(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.prestasi.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
