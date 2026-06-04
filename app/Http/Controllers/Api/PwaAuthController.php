<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PwaToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PwaAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Generate token
        $plainToken = Str::random(64);
        $expiresAt = now()->addYear();

        PwaToken::create([
            'user_id' => $user->id,
            'token' => Hash::make($plainToken),
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'token' => $plainToken,
            'expires_at' => $expiresAt->toISOString(),
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'jabatan' => $user->jabatan,
                'username' => $user->username,
            ],
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'jabatan' => $user->jabatan,
                'username' => $user->username,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->header('X-PWA-Token');

        if ($token) {
            PwaToken::where('user_id', $request->user()->id)
                ->where('token', Hash::make($token))
                ->delete();
        }

        return response()->json(['message' => 'Berhasil logout.']);
    }

    public function refresh(Request $request): JsonResponse
    {
        $token = $request->header('X-PWA-Token');

        $pwaToken = PwaToken::where('user_id', $request->user()->id)
            ->where('token', Hash::make($token))
            ->first();

        if (! $pwaToken) {
            return response()->json(['message' => 'Token tidak ditemukan.'], 404);
        }

        $pwaToken->update([
            'expires_at' => now()->addYear(),
        ]);

        return response()->json([
            'message' => 'Token berhasil di-refresh.',
            'expires_at' => $pwaToken->fresh()->expires_at->toISOString(),
        ]);
    }
}
