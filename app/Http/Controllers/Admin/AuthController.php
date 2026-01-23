<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {

            if (!auth()->user()->isAdmin()) {
                auth()->logout();
                return back()->withErrors(['email' => 'Access denied']);
            }

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('admin.login');
    }

}
