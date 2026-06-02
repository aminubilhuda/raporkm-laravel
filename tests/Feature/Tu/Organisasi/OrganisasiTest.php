<?php

namespace Tests\Feature\Tu\Organisasi;

use App\Models\Organisasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisasiTest extends TestCase
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

    public function test_tu_can_view_organisasi_index(): void
    {
        Organisasi::factory()->count(2)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.organisasi.index'))
            ->assertStatus(200)
            ->assertSee('Organisasi');
    }

    public function test_tu_can_create_organisasi(): void
    {
        $this->actingAs($this->tu)->post(route('tu.organisasi.store'), [
            'nama_organisasi' => 'OSIS',
            'keterangan' => 'Organisasi Siswa Intra Sekolah',
        ])->assertRedirect();

        $this->assertDatabaseHas('organisasi', ['nama_organisasi' => 'OSIS']);
    }

    public function test_create_organisasi_validates_nama_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.organisasi.store'), ['keterangan' => 'kosong'])
            ->assertSessionHasErrors('nama_organisasi');
    }

    public function test_tu_can_update_organisasi(): void
    {
        $o = Organisasi::factory()->create(['nama_organisasi' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.organisasi.update', $o), [
            'nama_organisasi' => 'Baru',
        ])->assertRedirect();

        $this->assertDatabaseHas('organisasi', ['id' => $o->id, 'nama_organisasi' => 'Baru']);
    }

    public function test_tu_can_delete_organisasi(): void
    {
        $o = Organisasi::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.organisasi.destroy', $o))
            ->assertRedirect();

        $this->assertSoftDeleted('organisasi', ['id' => $o->id]);
    }

    public function test_guru_cannot_access_tu_organisasi(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.organisasi.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
