<?php

namespace Tests\Feature\Guru\Organisasi;

use App\Models\Organisasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->guru()->create();
    }

    public function test_guru_can_view_organisasi_index(): void
    {
        Organisasi::factory()->create(['nama_organisasi' => 'OSIS']);

        $this->actingAs(User::first())
            ->get(route('guru.organisasi.index'))
            ->assertStatus(200)
            ->assertSee('OSIS');
    }

    public function test_guru_sees_empty_state(): void
    {
        $this->actingAs(User::first())
            ->get(route('guru.organisasi.index'))
            ->assertStatus(200)
            ->assertSee('Belum ada data');
    }
}
