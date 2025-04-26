<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerifyMFA
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth() -> check() && !Session ::has('mfa_verified')) {
            return redirect() -> route('mfa.verify');
        }

        return $next($request);
    }
}
