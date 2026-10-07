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
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'cabang_id' => $request->cabang_id,
            'password' => Hash::make($request->password),
            'role' => 'karyawan',
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return back()->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nik' => 'required|unique:users,nik,' . $user->id,
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'cabang_id' => 'required|exists:cabang_kantors,id',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user->update([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'cabang_id' => $request->cabang_id,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return back()->with('success', 'Karyawan berhasil dihapus.');
    }
}