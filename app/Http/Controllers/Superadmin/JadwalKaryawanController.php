<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalKaryawan;
use App\Models\User;
use App\Models\MasterShift;

class JadwalKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $karyawans = User::role('karyawan')->get();
        $shifts = MasterShift::all();
        
        $query = JadwalKaryawan::with(['user', 'shift'])->orderBy('created_at', 'desc');

        // Optional filtering by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        $jadwals = $query->paginate(15);

        return view('superadmin.jadwal', compact('jadwals', 'karyawans', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:master_shifts,id',
        ]);

        JadwalKaryawan::updateOrCreate(
            ['user_id' => $request->user_id],
            ['shift_id' => $request->shift_id]
        );

        return back()->with('success', 'Jadwal shift karyawan berhasil disimpan!');
    }

    public function destroy($id)
    {
        $jadwal = JadwalKaryawan::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus!');
    }
}
