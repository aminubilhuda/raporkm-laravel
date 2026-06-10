<?php

namespace Tests\Feature\Tu\Prakerin;

use App\Models\Kelas;
use App\Models\Prakerin;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrakerinPesertaTest extends TestCase
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

    public function test_tu_can_view_peserta_index(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.prakerin.peserta'))
            ->assertStatus(200)
            ->assertSee('Peserta');
    }

    public function test_tu_can_add_peserta(): void
    {
        $prakerin = Prakerin::factory()->create();
        $siswa = Siswa::factory()->create();
        $kelas = Kelas::factory()->create();

        $this->actingAs($this->tu)->post(route('tu.prakerin.peserta.store'), [
            'prakerin_id' => $prakerin->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('siswa_prakerin', [
            'prakerin_id' => $prakerin->id,
            'siswa_id' => $siswa->id,
        ]);
    }

    public function test_add_peserta_validates_siswa_required(): void
    {
        $prakerin = Prakerin::factory()->create();
        $kelas = Kelas::factory()->create();

        $this->actingAs($this->tu)
            ->post(route('tu.prakerin.peserta.store'), [
                'prakerin_id' => $prakerin->id,
                'kelas_id' => $kelas->id,
                'tahun_pelajaran_id' => $this->tahun->id,
                'semester_id' => $this->semester->id,
            ])
            ->assertSessionHasErrors('siswa_id');
    }

    public function test_tu_can_delete_peserta(): void
    {
        $p = SiswaPrakerin::factory()->create();

        $this->actingAs($this->tu)
            ->delete(route('tu.prakerin.peserta.destroy', $p))
            ->assertRedirect();

        $this->assertDatabaseHas('siswa_prakerin', ['id' => $p->id]);
        $this->assertNotNull($p->fresh()->deleted_at);
    }

    public function test_guru_cannot_access_peserta(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.prakerin.peserta'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}
