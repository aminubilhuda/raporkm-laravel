<?php

namespace Tests\Feature\Tu\P5bk;

use App\Models\Kelas;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P5bkProyekTest extends TestCase
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

    public function test_tu_can_view_proyek_index(): void
    {
        ProyekKelas::factory()->count(2)->create();

        $this->actingAs($this->tu)
            ->get(route('tu.p5bk.proyek.index'))
            ->assertStatus(200)
            ->assertSee('Proyek');
    }

    public function test_tu_can_create_proyek(): void
    {
        $kelas = Kelas::factory()->create();
        $tema = ProyekTema::factory()->create();

        $this->actingAs($this->tu)->post(route('tu.p5bk.proyek.store'), [
            'kelas_id' => $kelas->id,
            'proyek_tema_id' => $tema->id,
            'judul' => 'Proyek Lingkungan',
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyek_kelas', ['judul' => 'Proyek Lingkungan']);
    }

    public function test_create_proyek_validates_kelas_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.p5bk.proyek.store'), ['judul' => 'tanpa kelas'])
            ->assertSessionHasErrors('kelas_id');
    }

    public function test_tu_can_update_proyek(): void
    {
        $p = ProyekKelas::factory()->create(['judul' => 'Lama']);

        $this->actingAs($this->tu)->put(route('tu.p5bk.proyek.update', $p), [
            'kelas_id' => $p->kelas_id,
            'proyek_tema_id' => $p->proyek_tema_id,
            'judul' => 'Baru',
            'tahun_pelajaran_id' => $p->tahun_pelajaran_id,
            'semester_id' => $p->semester_id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyek_kelas', ['id' => $p->id, 'judul' => 'Baru']);
    }

    public function test_tu_can_delete_proyek(): void
    {
        $p = ProyekKelas::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.p5bk.proyek.destroy', $p))
            ->assertRedirect();

        $this->assertSoftDeleted('proyek_kelas', ['id' => $p->id]);
    }

    public function test_guru_cannot_access_tu_proyek(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.p5bk.proyek.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
