<?php

namespace Tests\Feature\Tu\P5bk;

use App\Models\Dimensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P5bkDimensiTest extends TestCase
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

    public function test_tu_can_view_p5bk_index(): void
    {
        Dimensi::factory()->count(2)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.p5bk.index'))
            ->assertStatus(200)
            ->assertSee('P5BK');
    }

    public function test_tu_can_create_dimensi(): void
    {
        $this->actingAs($this->tu)->post(route('tu.p5bk.dimensi.store'), [
            'nama' => 'Beriman',
            'keterangan' => 'Profil Pelajar Pancasila',
            'urutan' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('dimensi', ['nama' => 'Beriman']);
    }

    public function test_create_dimensi_validates_nama_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.p5bk.dimensi.store'), ['keterangan' => 'kosong'])
            ->assertSessionHasErrors('nama');
    }

    public function test_tu_can_update_dimensi(): void
    {
        $d = Dimensi::factory()->create(['nama' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.p5bk.dimensi.update', $d), [
            'nama' => 'Baru',
            'urutan' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('dimensi', ['id' => $d->id, 'nama' => 'Baru']);
    }

    public function test_tu_can_delete_dimensi(): void
    {
        $d = Dimensi::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.p5bk.dimensi.destroy', $d))
            ->assertRedirect();

        $this->assertSoftDeleted('dimensi', ['id' => $d->id]);
    }

    public function test_guru_cannot_access_tu_p5bk(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.p5bk.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
