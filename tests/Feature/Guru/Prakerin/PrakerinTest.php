<?php

namespace Tests\Feature\Guru\Prakerin;

use App\Models\Prakerin;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrakerinTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    protected function setUp(): void
    {
        parent::setUp();
        TahunPelajaran::factory()->create();
        Semester::factory()->create();
        $this->guru = User::factory()->guru()->create();
    }

    public function test_guru_can_view_prakerin_index(): void
    {
        Prakerin::factory()->create(['nama_perusahaan' => 'PT Contoh']);

        $this->actingAs($this->guru)
            ->get(route('guru.prakerin.index'))
            ->assertStatus(200)
            ->assertSee('PT Contoh');
    }

    public function test_guru_sees_empty_state_when_no_data(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.prakerin.index'))
            ->assertStatus(200)
            ->assertSee('Belum ada data');
    }
}
