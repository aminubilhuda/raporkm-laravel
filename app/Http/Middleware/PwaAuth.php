<?php

namespace App\Http\Middleware;

use App\Models\PwaToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class PwaAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-PWA-Token');

        if (! $token) {
            return response()->json(['message' => 'PWA token tidak ditemukan.'], 401);
        }

        $pwaToken = PwaToken::where('expires_at', '>', now())->first();

        if (! $pwaToken) {
            return response()->json(['message' => 'Token tidak valid atau sudah expired.'], 401);
        }

        // Verify hash
        $validToken = PwaToken::where('expires_at', '>', now())->get()->first(function ($t) use ($token) {
            return Hash::check($token, $t->token);
        });

        if (! $validToken) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        $user = User::find($validToken->user_id);

        if (! $user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 401);
        }

        $request->setUser($user);

        return $next($request);
    }
}
