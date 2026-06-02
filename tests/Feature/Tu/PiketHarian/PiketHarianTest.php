<?php

namespace Tests\Feature\Tu\PiketHarian;

use App\Models\PiketHarian;
use App\Models\RefHari;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PiketHarianTest extends TestCase
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
        RefHari::factory()->create(['nama' => 'Senin', 'urutan' => 1]);
    }

    public function test_tu_can_view_piket_index(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.piket-harian.index'))
            ->assertStatus(200)
            ->assertSee('Piket');
    }

    public function test_tu_can_add_piket(): void
    {
        $hari = RefHari::first();
        $user = User::factory()->guru()->create();

        $this->actingAs($this->tu)->post(route('tu.piket-harian.store'), [
            'user_id' => $user->id,
            'hari_id' => $hari->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('piket_harian', [
            'user_id' => $user->id,
            'hari_id' => $hari->id,
        ]);
    }

    public function test_add_piket_validates_user_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.piket-harian.store'), ['hari_id' => 1])
            ->assertSessionHasErrors('user_id');
    }

    public function test_tu_can_delete_piket(): void
    {
        $p = PiketHarian::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.piket-harian.destroy', $p))
            ->assertRedirect();

        $this->assertDatabaseMissing('piket_harian', ['id' => $p->id]);
    }

    public function test_guru_cannot_access_tu_piket(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.piket-harian.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
