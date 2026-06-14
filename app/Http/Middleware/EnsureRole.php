<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Support both numeric jabatan (2,3,4) and role names (TU,Guru,Kepsek)
        $roleMap = [
            '2' => 'TU',
            '3' => 'Guru',
            '4' => 'Kepsek',
        ];

        $normalizedRoles = array_map(fn ($role) => $roleMap[$role] ?? $role, $roles);

        if (! $request->user()->hasAnyRole($normalizedRoles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
