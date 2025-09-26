<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // ใช้แบบ: ->middleware('role:admin,manager')
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole($roles)) {
            return response()->json(['message' => 'Forbidden: insufficient role'], 403);
        }
        return $next($request);
    }
}
