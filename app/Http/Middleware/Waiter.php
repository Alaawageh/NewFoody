<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Waiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->user_type == 1 || auth()->user()->user_type == 5) {
            return $next($request);
        } else {
            return response()->json(['error' => 'FORBIDDEN'],Response::HTTP_FORBIDDEN) ;
        }
    }
}
