<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->tataUsaha()->create([
            'password' => Hash::make('password123'),
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_update_profile(): void
    {
        $response = $this->putJson('/api/v1/profile', [
            'nama' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'nama' => 'Updated Name']);
    }

    public function test_update_profile_validation(): void
    {
        $response = $this->putJson('/api/v1/profile', [
            'email' => 'invalid-email',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    public function test_change_password(): void
    {
        $response = $this->putJson('/api/v1/profile/password', [
            'current_password' => 'password123',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertTrue(Hash::check('newpassword456', $this->user->fresh()->password));
    }

    public function test_change_password_wrong_current(): void
    {
        $response = $this->putJson('/api/v1/profile/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_change_password_validation(): void
    {
        $response = $this->putJson('/api/v1/profile/password', [
            'current_password' => 'password123',
            'new_password' => '123',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['new_password']);
    }
}
