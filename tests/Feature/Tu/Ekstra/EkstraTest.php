<?php

namespace Tests\Feature\Tu\Ekstra;

use App\Models\Eskul;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstraTest extends TestCase
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

    public function test_tu_can_view_ekstra_index(): void
    {
        Eskul::factory()->count(2)->create();

        $response = $this->actingAs($this->tu)->get(route('tu.ekstra.index'));

        $response->assertStatus(200);
        $response->assertSee('Ekstrakurikuler');
    }

    public function test_tu_can_create_eskul(): void
    {
        $response = $this->actingAs($this->tu)->post(route('tu.ekstra.store'), [
            'nama_eskul' => 'Pramuka',
            'keterangan' => 'Ekstra wajib',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('eskul', ['nama_eskul' => 'Pramuka']);
    }

    public function test_create_eskul_validates_required_nama(): void
    {
        $response = $this->actingAs($this->tu)->post(route('tu.ekstra.store'), [
            'keterangan' => 'No name',
        ]);

        $response->assertSessionHasErrors('nama_eskul');
    }

    public function test_tu_can_update_eskul(): void
    {
        $eskul = Eskul::factory()->create(['nama_eskul' => 'Lama']);

        $response = $this->actingAs($this->tu)->put(route('tu.ekstra.update', $eskul), [
            'nama_eskul' => 'Baru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('eskul', ['id' => $eskul->id, 'nama_eskul' => 'Baru']);
    }

    public function test_tu_can_delete_eskul(): void
    {
        $eskul = Eskul::factory()->create();

        $response = $this->actingAs($this->tu)->delete(route('tu.ekstra.destroy', $eskul));

        $response->assertRedirect();
        $this->assertSoftDeleted('eskul', ['id' => $eskul->id]);
    }

    public function test_guru_cannot_access_tu_ekstra(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.ekstra.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
