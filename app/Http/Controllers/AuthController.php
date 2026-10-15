<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Menampilkan Halaman Login
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('superadmin')) {
                return redirect('/admin');
            }
            return redirect()->route('karyawan.presensi.create');
        }

        return view('login');
    }

    // Memproses Data Login (Mendukung Email & NIK)
    public function authenticate(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $isEmail = filter_var($request->login, FILTER_VALIDATE_EMAIL);
        $authenticated = false;

        if ($isEmail) {
            $credentials = [
                'email'    => $request->login,
                'password' => $request->password,
            ];
            $authenticated = Auth::attempt($credentials, $request->boolean('remember'));
        } else {
            // Login via NIK pada relasi user_profiles
            $user = User::whereHas('profile', function ($query) use ($request) {
                $query->where('nik', $request->login);
            })->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user, $request->boolean('remember'));
                $authenticated = true;
            }
        }

        if ($authenticated) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->hasRole('superadmin')) {
                return redirect('/admin');
            }

            return redirect()->route('karyawan.presensi.create');
        }

        return back()->withErrors([
            'login' => 'Email/NIK atau password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}