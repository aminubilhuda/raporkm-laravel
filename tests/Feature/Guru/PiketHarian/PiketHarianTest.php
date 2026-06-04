<?php

namespace Tests\Feature\Guru\PiketHarian;

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

    private User $guru;

    protected function setUp(): void
    {
        parent::setUp();
        TahunPelajaran::factory()->create();
        Semester::factory()->create();
        $this->guru = User::factory()->guru()->create();
        RefHari::factory()->create(['nama' => 'Senin', 'urutan' => 1]);
    }

    public function test_guru_can_view_piket_index(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.piket-harian.index'))
            ->assertStatus(200)
            ->assertSee('Piket Harian');
    }

    public function test_guru_sees_piket_data(): void
    {
        $guruPiket = User::factory()->guru()->create(['nama' => 'Guru Piket']);
        $hari = RefHari::first();
        PiketHarian::factory()->create([
            'user_id' => $guruPiket->id,
            'hari_id' => $hari->id,
        ]);

        $this->actingAs($this->guru)
            ->get(route('guru.piket-harian.index'))
            ->assertStatus(200)
            ->assertSee('Guru Piket')
            ->assertSee('Senin');
    }
}
