<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan Halaman Login
    public function index()
    {
        return view('login');
    }

    // Memproses Data Login
    public function authenticate(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Bisa diisi Email atau NIK
            'password' => 'required|string',
        ]);

        // Cek apakah input berupa format email atau bukan (NIK)
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password
        ];

        // Coba Login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Arahkan ke DashboardController (yang akan memilah Superadmin/Karyawan)
            return redirect()->intended(route('dashboard'));
        }

        // Jika Gagal
        return back()->withErrors([
            'login' => 'Email/NIK atau password salah.',
        ])->onlyInput('login');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}