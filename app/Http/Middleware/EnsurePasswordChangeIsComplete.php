<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChangeIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->requiresPasswordChange()) {
            return $next($request);
        }

        if ($request->routeIs('password.setup.edit', 'password.setup.update', 'logout')) {
            return $next($request);
        }

        return redirect()->route('password.setup.edit');
    }
}
