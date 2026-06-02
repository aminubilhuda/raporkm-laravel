<?php

namespace Tests\Feature\Tu\Prakerin;

use App\Models\Prakerin;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrakerinTest extends TestCase
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

    public function test_tu_can_view_prakerin_index(): void
    {
        Prakerin::factory()->count(2)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.prakerin.index'))
            ->assertStatus(200)
            ->assertSee('Prakerin');
    }

    public function test_tu_can_create_prakerin(): void
    {
        $this->actingAs($this->tu)->post(route('tu.prakerin.store'), [
            'nama_perusahaan' => 'PT ABC',
            'alamat' => 'Jl. Industri',
            'kontak' => '08123',
            'PIC' => 'Budi',
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('prakerin', ['nama_perusahaan' => 'PT ABC']);
    }

    public function test_create_prakerin_validates_nama_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.prakerin.store'), ['alamat' => 'tanpa nama'])
            ->assertSessionHasErrors('nama_perusahaan');
    }

    public function test_tu_can_update_prakerin(): void
    {
        $p = Prakerin::factory()->create(['nama_perusahaan' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.prakerin.update', $p), [
            'nama_perusahaan' => 'Baru',
        ])->assertRedirect();

        $this->assertDatabaseHas('prakerin', ['id' => $p->id, 'nama_perusahaan' => 'Baru']);
    }

    public function test_tu_can_delete_prakerin(): void
    {
        $p = Prakerin::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.prakerin.destroy', $p))
            ->assertRedirect();

        $this->assertDatabaseMissing('prakerin', ['id' => $p->id]);
    }

    public function test_guru_cannot_access_tu_prakerin(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.prakerin.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
