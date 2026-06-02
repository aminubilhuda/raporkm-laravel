<?php

namespace Tests\Feature\Tu\P5bk;

use App\Models\ProyekTema;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P5bkTemaTest extends TestCase
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

    public function test_tu_can_view_tema_index(): void
    {
        ProyekTema::factory()->count(2)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.p5bk.tema.index'))
            ->assertStatus(200)
            ->assertSee('Tema');
    }

    public function test_tu_can_create_tema(): void
    {
        $tp = TahunPelajaran::factory()->create();
        $s = Semester::factory()->create();

        $this->actingAs($this->tu)->post(route('tu.p5bk.tema.store'), [
            'nama_tema' => 'Gaya Hidup Berkelanjutan',
            'keterangan' => 'Tema P5',
            'tahun_pelajaran_id' => $tp->id,
            'semester_id' => $s->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyek_tema', ['nama_tema' => 'Gaya Hidup Berkelanjutan']);
    }

    public function test_create_tema_validates_nama_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.p5bk.tema.store'), ['keterangan' => 'kosong'])
            ->assertSessionHasErrors('nama_tema');
    }

    public function test_tu_can_update_tema(): void
    {
        $t = ProyekTema::factory()->create(['nama_tema' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.p5bk.tema.update', $t), [
            'nama_tema' => 'Baru',
            'tahun_pelajaran_id' => $t->tahun_pelajaran_id,
            'semester_id' => $t->semester_id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyek_tema', ['id' => $t->id, 'nama_tema' => 'Baru']);
    }

    public function test_tu_can_delete_tema(): void
    {
        $t = ProyekTema::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.p5bk.tema.destroy', $t))
            ->assertRedirect();

        $this->assertSoftDeleted('proyek_tema', ['id' => $t->id]);
    }

    public function test_guru_cannot_access_tu_tema(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.p5bk.tema.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
