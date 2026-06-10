<?php

namespace Tests\Feature\Api\V1;

use App\Models\Dimensi;
use App\Models\Elemen;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class P5bkReferenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->tataUsaha()->create();
        Sanctum::actingAs($this->user);
    }

    // ── Dimensi ──

    public function test_dimensi_index(): void
    {
        Dimensi::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/tu/p5bk/dimensi');

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_dimensi_store(): void
    {
        $response = $this->postJson('/api/v1/tu/p5bk/dimensi', [
            'nama' => 'Dimensi Baru',
            'keterangan' => 'Deskripsi',
            'urutan' => 1,
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
        $this->assertDatabaseHas('dimensi', ['nama' => 'Dimensi Baru']);
    }

    public function test_dimensi_store_validation(): void
    {
        $response = $this->postJson('/api/v1/tu/p5bk/dimensi', []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['nama']);
    }

    public function test_dimensi_update(): void
    {
        $dimensi = Dimensi::factory()->create();

        $response = $this->putJson('/api/v1/tu/p5bk/dimensi/'.$dimensi->id, [
            'nama' => 'Updated',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('dimensi', ['id' => $dimensi->id, 'nama' => 'Updated']);
    }

    public function test_dimensi_destroy(): void
    {
        $dimensi = Dimensi::factory()->create();

        $response = $this->deleteJson('/api/v1/tu/p5bk/dimensi/'.$dimensi->id);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertSoftDeleted('dimensi', ['id' => $dimensi->id]);
    }

    public function test_dimensi_destroy_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/tu/p5bk/dimensi/9999');

        $response->assertNotFound()->assertJsonPath('success', false);
    }

    // ── Elemen ──

    public function test_elemen_index(): void
    {
        $dimensi = Dimensi::factory()->create();
        Elemen::factory()->count(2)->create(['dimensi_id' => $dimensi->id]);

        $response = $this->getJson('/api/v1/tu/p5bk/elemen?dimensi_id='.$dimensi->id);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_elemen_store(): void
    {
        $dimensi = Dimensi::factory()->create();

        $response = $this->postJson('/api/v1/tu/p5bk/elemen', [
            'dimensi_id' => $dimensi->id,
            'nama' => 'Elemen Baru',
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
        $this->assertDatabaseHas('elemen', ['nama' => 'Elemen Baru']);
    }

    public function test_elemen_store_validation(): void
    {
        $response = $this->postJson('/api/v1/tu/p5bk/elemen', []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['dimensi_id', 'nama']);
    }

    public function test_elemen_update(): void
    {
        $elemen = Elemen::factory()->create();

        $response = $this->putJson('/api/v1/tu/p5bk/elemen/'.$elemen->id, [
            'nama' => 'Updated',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_elemen_destroy(): void
    {
        $elemen = Elemen::factory()->create();

        $response = $this->deleteJson('/api/v1/tu/p5bk/elemen/'.$elemen->id);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseMissing('elemen', ['id' => $elemen->id]);
    }

    // ── SubElemen ──

    public function test_sub_elemen_index(): void
    {
        $elemen = Elemen::factory()->create();
        SubElemen::factory()->count(2)->create(['elemen_id' => $elemen->id]);

        $response = $this->getJson('/api/v1/tu/p5bk/sub-elemen?elemen_id='.$elemen->id);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_sub_elemen_store(): void
    {
        $elemen = Elemen::factory()->create();

        $response = $this->postJson('/api/v1/tu/p5bk/sub-elemen', [
            'elemen_id' => $elemen->id,
            'nama' => 'Sub Elemen Baru',
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
        $this->assertDatabaseHas('sub_elemen', ['nama' => 'Sub Elemen Baru']);
    }

    public function test_sub_elemen_store_validation(): void
    {
        $response = $this->postJson('/api/v1/tu/p5bk/sub-elemen', []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['elemen_id', 'nama']);
    }

    public function test_sub_elemen_update(): void
    {
        $sub = SubElemen::factory()->create();

        $response = $this->putJson('/api/v1/tu/p5bk/sub-elemen/'.$sub->id, [
            'nama' => 'Updated',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_sub_elemen_destroy(): void
    {
        $sub = SubElemen::factory()->create();

        $response = $this->deleteJson('/api/v1/tu/p5bk/sub-elemen/'.$sub->id);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseMissing('sub_elemen', ['id' => $sub->id, 'deleted_at' => null]);
    }
}
