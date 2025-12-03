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
     * Usage example in routes:
     *  - ->middleware(['auth', 'role:admin'])
     *  - ->middleware(['auth', 'role:member'])
     *  - ->middleware(['auth', 'role:seller'])
     *  - ->middleware(['auth', 'role:member,seller'])
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        foreach ($roles as $role) {
            $role = strtolower(trim($role));

            // Admin
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }

            // Member / customer / buyer:
            if (in_array($role, ['member', 'customer', 'buyer'], true) && $user->isMember()) {
                return $next($request);
            }

            // Seller
            if ($role === 'seller' && $user->isSeller()) {
                return $next($request);
            }
        }

        // resources/views/errors/403.blade.php
        abort(403);
    }
}
