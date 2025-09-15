<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;



class CheckUserType
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check() || Auth::user()->user_type !== $role) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
