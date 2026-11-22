<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->withInput($request->only('email'));
        }

        if (!in_array($user->role, ['admin', 'operator', 'customer'], true)) {
            return back()
                ->withErrors([
                    'email' => 'Role akun tidak valid. Hubungi administrator.',
                ])
                ->withInput($request->only('email'));
        }

        Auth::login($user);

        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        if ($user->role === 'operator') {
            return redirect()->route('operator.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
