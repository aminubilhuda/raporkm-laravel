<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SekolahResource;
use App\Http\Resources\V1\UserResource;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if ($user->trashed()) {
            throw ValidationException::withMessages([
                'username' => ['Akun tidak aktif.'],
            ]);
        }

        // Revoke existing tokens for this device
        $deviceName = $validated['device_name'] ?? 'android-app';
        $user->tokens()->where('name', $deviceName)->delete();

        // Create new token
        $token = $user->createToken($deviceName);
        $plainTextToken = $token->plainTextToken;

        // Load relationships
        $user->load('ptk');

        // Get school data with active year/semester
        $sekolah = Sekolah::with(['tahunPelajaran', 'semester'])->first();

        // Get active year/semester
        $tahunAktif = $sekolah?->tahunPelajaran;
        $semesterAktif = $sekolah?->semester;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $plainTextToken,
                'user' => new UserResource($user),
                'sekolah' => $sekolah ? new SekolahResource($sekolah) : null,
                'tahun_aktif' => $tahunAktif,
                'semester_aktif' => $semesterAktif,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('ptk');

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $sekolah->load(['tahunPelajaran', 'semester']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'sekolah' => $sekolah ? new SekolahResource($sekolah) : null,
                'tahun_aktif' => $sekolah->tahunPelajaran ?? null,
                'semester_aktif' => $sekolah->semester ?? null,
            ],
        ]);
    }

    public function registerFcm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => ['required', 'string', 'max:500'],
            'device_id' => ['nullable', 'string', 'max:255'],
        ]);

        $request->user()->update([
            'fcm_token' => $validated['fcm_token'],
            'device_name' => $validated['device_id'] ?? 'android-app',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token berhasil didaftarkan.',
        ]);
    }

    public function unregisterFcm(Request $request): JsonResponse
    {
        $request->user()->update([
            'fcm_token' => null,
            'device_name' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token berhasil dihapus.',
        ]);
    }
}
