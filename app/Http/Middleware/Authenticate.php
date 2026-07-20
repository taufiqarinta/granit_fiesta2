<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Authenticate the user and block inactive accounts from continuing.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        $user = $request->user();

        if ($user && method_exists($user, 'isActive') && ! $user->isActive()) {
            Auth::guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin sales atau tim IT untuk mengaktifkannya kembali.',
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'id_customer' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin sales atau tim IT untuk mengaktifkannya kembali.',
            ]);
        }

        return $next($request);
    }
}
