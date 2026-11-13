<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        if ($user->isSuspended()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }

        if (!$user->isVerified()) {
            return redirect()->route('profile.show')->with(
                'warning',
                'Your account is not verified yet. Please submit your student ID verification and await admin approval before you can list items or request to borrow.'
            );
        }

        return $next($request);
    }
}

