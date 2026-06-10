<?php

namespace Tests\Feature\Api\V1;

use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $tuUser;

    private User $guruUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create school data needed for login response
        $tp = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => 1]);
        $sem = Semester::create(['nama' => 'Ganjil', 'urutan' => 1, 'status' => 1]);
        Sekolah::create([
            'npsn' => '12345678',
            'nama_sekolah' => 'SMK Abdi Negara Tuban',
            'tahun_aktif' => $tp->id,
            'semester_aktif' => $sem->id,
        ]);

        $this->tuUser = User::factory()->create([
            'jabatan' => 2,
            'username' => 'tu01',
            'password' => bcrypt('password123'),
        ]);

        $this->guruUser = User::factory()->create([
            'jabatan' => 3,
            'username' => 'guru01',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_login_success(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'tu01',
            'password' => 'password123',
            'device_name' => 'test-device',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'nama', 'username', 'jabatan', 'jabatan_label'],
                    'sekolah',
                    'tahun_aktif',
                    'semester_aktif',
                ],
            ]);
    }

    public function test_login_wrong_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'tu01',
            'password' => 'wrongpassword',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['username']);
    }

    public function test_login_wrong_username(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'nonexistent',
            'password' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['username']);
    }

    public function test_login_missing_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['username', 'password']);
    }

    public function test_me_authenticated(): void
    {
        $token = $this->tuUser->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $this->tuUser->id,
                        'username' => 'tu01',
                        'jabatan' => 2,
                    ],
                ],
            ]);
    }

    public function test_me_unauthenticated(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertUnauthorized();
    }

    public function test_me_guru_role(): void
    {
        $token = $this->guruUser->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'jabatan' => 3,
                        'jabatan_label' => 'Guru',
                    ],
                ],
            ]);
    }

    public function test_logout(): void
    {
        $token = $this->tuUser->createToken('test-device')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Logout
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);

        // Verify token is revoked
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_fcm_register(): void
    {
        $token = $this->tuUser->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/v1/auth/fcm', [
            'fcm_token' => 'dGxOabc123xyz',
            'device_id' => 'android-test',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'FCM token berhasil didaftarkan.',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->tuUser->id,
            'fcm_token' => 'dGxOabc123xyz',
            'device_name' => 'android-test',
        ]);
    }

    public function test_fcm_register_missing_token(): void
    {
        $token = $this->tuUser->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/v1/auth/fcm', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['fcm_token']);
    }

    public function test_fcm_unregister(): void
    {
        $this->tuUser->update(['fcm_token' => 'old-token']);

        $token = $this->tuUser->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson('/api/v1/auth/fcm');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'FCM token berhasil dihapus.',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->tuUser->id,
            'fcm_token' => null,
        ]);
    }

    public function test_login_revokes_previous_token(): void
    {
        // Create first token
        $this->tuUser->createToken('android-app');

        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Login again with same device name
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'tu01',
            'password' => 'password123',
            'device_name' => 'android-app',
        ]);

        $response->assertOk();

        // Old token should be revoked, only new one exists
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_different_devices_keep_tokens(): void
    {
        $this->tuUser->createToken('android-app');
        $this->tuUser->createToken('ios-app');

        $this->assertDatabaseCount('personal_access_tokens', 2);

        // Login with different device should not revoke others
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'tu01',
            'password' => 'password123',
            'device_name' => 'web-browser',
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 3);
    }

    public function test_referensi_agama(): void
    {
        $token = $this->tuUser->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/v1/referensi/agama');

        $response->assertOk();
    }

    public function test_referensi_unauthenticated(): void
    {
        $response = $this->getJson('/api/v1/referensi/agama');

        $response->assertUnauthorized();
    }
}
