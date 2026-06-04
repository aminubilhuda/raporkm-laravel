<?php

namespace Tests\Feature\Guru\Ekstra;

use App\Models\Eskul;
use App\Models\PembinaEskul;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstraTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private TahunPelajaran $tahun;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();
        $this->guru = User::factory()->guru()->create();
    }

    public function test_guru_can_view_ekstra_index(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.ekstra.index'))
            ->assertStatus(200)
            ->assertSee('Ekstrakurikuler');
    }

    public function test_guru_sees_only_their_eskuls(): void
    {
        $eskulSaya = Eskul::factory()->create(['nama_eskul' => 'Eskul Saya']);
        $eskulLain = Eskul::factory()->create(['nama_eskul' => 'Eskul Lain']);

        PembinaEskul::factory()->create([
            'eskul_id' => $eskulSaya->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
        ]);
        PembinaEskul::factory()->create([
            'eskul_id' => $eskulLain->id,
            'user_id' => User::factory()->guru()->create()->id,
            'tahun_pelajaran_id' => $this->tahun->id,
        ]);

        $response = $this->actingAs($this->guru)->get(route('guru.ekstra.index'));
        $response->assertStatus(200);
        $response->assertSee('Eskul Saya');
        $response->assertDontSee('Eskul Lain');
    }
}
