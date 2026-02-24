<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'provider') {
            return redirect()->route('provider.login')
                ->with('error', 'Please log in as a service provider to continue.');
        }

        // Ensure they actually have a provider profile
        if (!Auth::user()->serviceProvider) {
            Auth::logout();
            return redirect()->route('provider.login')
                ->with('error', 'No provider profile found for this account.');
        }

        return $next($request);
    }
}