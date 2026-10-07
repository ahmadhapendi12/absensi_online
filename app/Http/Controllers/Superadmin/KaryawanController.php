<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CabangKantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = User::with('cabang')->where('role', 'karyawan')->get();
        $cabangs = CabangKantor::all();
        return view('superadmin.karyawan', compact('karyawans', 'cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:users',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'cabang_id' => 'required|exists:cabang_kantors,id',
            'password' => 'required|min:6',
        ]);

        User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'cabang_id' => $request->cabang_id,
            'password' => Hash::make($request->password),
            'role' => 'karyawan'
        ]);

        return back()->with('success', 'Karyawan berhasil ditambahkan.');
    }
}