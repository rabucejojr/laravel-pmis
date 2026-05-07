<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! $request->user()->hasRole($roles)) {
            $required = implode(', ', array_map('ucfirst', $roles));
            abort(403, "This page requires one of the following roles: {$required}.");
        }

        return $next($request);
    }
}
