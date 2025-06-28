<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShareJwtWithFrontend
{
    public function handle(Request $request, Closure $next)
    {
        if ($token = $request->session()->get('access_token')) {
            // Share token with Inertia frontend
            inertia()->share([
                'access_token' => $token
            ]);
        }

        return $next($request);
    }
}
