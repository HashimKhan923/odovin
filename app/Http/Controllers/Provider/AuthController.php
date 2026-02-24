<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'provider') {
            return redirect()->route('provider.dashboard');
        }
        return view('provider.auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Only allow users with provider role
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            if (Auth::user()->role !== 'provider') {
                Auth::logout();
                return back()->withErrors(['email' => 'This account is not a service provider.'])->withInput();
            }

            if (!Auth::user()->serviceProvider?->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your provider account has been deactivated.'])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->route('provider.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('provider.login')->with('success', 'Logged out successfully.');
    }
}