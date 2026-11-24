<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Batasi percobaan login: 5 kali per menit untuk kombinasi email + IP
        $key = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $detik = RateLimiter::availableIn($key);

            return back()
                ->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik."])
                ->withInput($request->only('email') + ['form_type' => 'login']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);

            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->withInput($request->only('email') + ['form_type' => 'login']);
        }

        if (!in_array($user->role, ['admin', 'operator', 'customer'], true)) {
            return back()
                ->withErrors(['email' => 'Role akun tidak valid. Hubungi administrator.'])
                ->withInput($request->only('email') + ['form_type' => 'login']);
        }

        RateLimiter::clear($key);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function register(Request $request)
    {
        // Error disimpan di bag "register" supaya tidak bentrok dengan error login
        $data = $request->validateWithBag('register', [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email belum benar.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.letters'   => 'Password harus mengandung huruf.',
            'password.numbers'   => 'Password harus mengandung angka.',
        ]);

        // Role diisi manual (bukan dari input) agar user tidak bisa mendaftar sebagai admin
        $user = new User();
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role     = 'customer';
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin'    => redirect()->route('dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            default    => redirect()->route('customer.dashboard'),
        };
    }
}
