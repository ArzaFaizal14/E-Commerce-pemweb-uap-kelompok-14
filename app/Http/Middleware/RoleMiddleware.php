<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Contoh penggunaan:
     *  - ->middleware(['auth', 'role:admin'])
     *  - ->middleware(['auth', 'role:member'])
     *  - ->middleware(['auth', 'role:admin,member']) // kalau mau salah satu
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        foreach ($roles as $role) {
            $role = strtolower(trim($role));

            // Admin: pakai helper isAdmin() dari model User
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }

            // Member: semua user dengan role 'member'
            if ($role === 'member' && $user->isMember()) {
                return $next($request);
            }
        }

        // Kalau tidak ada role yang cocok, tampilkan 403
        abort(403);
    }
}
