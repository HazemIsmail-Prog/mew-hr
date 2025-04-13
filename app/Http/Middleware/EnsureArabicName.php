<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureArabicName
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the name is in Arabic and not contains any english letters
        if ($request->user()->name && !preg_match('/^[\p{Arabic}]+$/u', $request->user()->name)) {
            return redirect()->route('settings.profile')->with('error', __('The name must be in Arabic'));
        }
        return $next($request);
    }
}
