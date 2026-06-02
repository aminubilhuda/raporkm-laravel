<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $lastActivity = session('last_activity');

        if ($lastActivity) {
            $timeout = config('e-rapor.session_timeout', 7200);

            if (now()->timestamp - $lastActivity > $timeout) {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect()->route('login')->with('status', 'Sesi anda telah berakhir. Silakan login kembali.');
            }
        }

        session(['last_activity' => now()->timestamp]);

        return $next($request);
    }
}
