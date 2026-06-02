<?php

namespace Tests\Feature\Tu\Kokurikuler;

use App\Models\DimensiKokurikuler;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KokurikulerTest extends TestCase
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

    public function test_tu_can_view_kokurikuler_index(): void
    {
        DimensiKokurikuler::factory()->count(2)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.kokurikuler.index'))
            ->assertStatus(200)
            ->assertSee('Kokurikuler');
    }

    public function test_tu_can_create_dimensi_kokurikuler(): void
    {
        $this->actingAs($this->tu)->post(route('tu.kokurikuler.dimensi.store'), [
            'nama' => 'Kebersihan',
            'keterangan' => 'Lingkungan bersih',
        ])->assertRedirect();

        $this->assertDatabaseHas('dimensi_kokurikuler', ['nama' => 'Kebersihan']);
    }

    public function test_create_dimensi_validates_nama_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.kokurikuler.dimensi.store'), ['keterangan' => 'kosong'])
            ->assertSessionHasErrors('nama');
    }

    public function test_tu_can_update_dimensi_kokurikuler(): void
    {
        $d = DimensiKokurikuler::factory()->create(['nama' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.kokurikuler.dimensi.update', $d), [
            'nama' => 'Baru',
        ])->assertRedirect();

        $this->assertDatabaseHas('dimensi_kokurikuler', ['id' => $d->id, 'nama' => 'Baru']);
    }

    public function test_tu_can_delete_dimensi_kokurikuler(): void
    {
        $d = DimensiKokurikuler::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.kokurikuler.dimensi.destroy', $d))
            ->assertRedirect();

        $this->assertSoftDeleted('dimensi_kokurikuler', ['id' => $d->id]);
    }

    public function test_guru_cannot_access_tu_kokurikuler(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.kokurikuler.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
