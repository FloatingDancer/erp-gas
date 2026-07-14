<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfDriver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->role === 'driver') {
            // Driver only allowed to access deliveries, profile, notifications, and logout
            $allowedPaths = [
                'deliveries',
                'deliveries/*',
                'my-profile',
                'profile',
                'profile/*',
                'notifications',
                'notifications/*',
                'logout',
                'switch-to-guest',
                'switch-to-driver',
            ];

            $isAllowed = false;
            foreach ($allowedPaths as $path) {
                if ($request->is($path)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                return redirect()->route('deliveries.index');
            }
        }

        return $next($request);
    }
}
