<?php

namespace Tests\Feature\Tu\Pengingat;

use App\Models\Pengingat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengingatTest extends TestCase
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

    public function test_tu_can_view_pengingat_index(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.pengingat.index'))
            ->assertStatus(200)
            ->assertSee('Pengingat');
    }

    public function test_tu_can_add_pengingat(): void
    {
        $this->actingAs($this->tu)->post(route('tu.pengingat.store'), [
            'judul' => 'Rapat Guru',
            'pesan' => 'Rapat bulanan',
            'untuk_role' => 3,
            'tanggal' => '2026-02-01',
        ])->assertRedirect();

        $this->assertDatabaseHas('pengingat', [
            'judul' => 'Rapat Guru',
            'untuk_role' => 3,
        ]);
    }

    public function test_add_pengingat_validates_judul_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.pengingat.store'), ['pesan' => 'tanpa judul', 'untuk_role' => 3, 'tanggal' => '2026-01-01'])
            ->assertSessionHasErrors('judul');
    }

    public function test_tu_can_delete_pengingat(): void
    {
        $p = Pengingat::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.pengingat.destroy', $p))
            ->assertRedirect();

        $this->assertDatabaseMissing('pengingat', ['id' => $p->id]);
    }

    public function test_guru_cannot_access_tu_pengingat(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.pengingat.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
