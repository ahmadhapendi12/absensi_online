<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\MasterShift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = MasterShift::all();
        return view('superadmin.shift', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:50',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_terlambat_menit' => 'nullable|integer',
            'lintas_hari' => 'nullable|boolean',
        ]);

        MasterShift::create([
            'nama_shift' => $request->nama_shift,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
            'toleransi_terlambat_menit' => $request->toleransi_terlambat_menit ?? 15,
            'lintas_hari' => $request->boolean('lintas_hari'),
        ]);

        return back()->with('success', 'Master Shift berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $shift = MasterShift::findOrFail($id);

        $request->validate([
            'nama_shift' => 'required|string|max:50',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_terlambat_menit' => 'nullable|integer',
            'lintas_hari' => 'nullable|boolean',
        ]);

        $shift->update([
            'nama_shift' => $request->nama_shift,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
            'toleransi_terlambat_menit' => $request->toleransi_terlambat_menit ?? 15,
            'lintas_hari' => $request->boolean('lintas_hari'),
        ]);

        return back()->with('success', 'Master Shift berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $shift = MasterShift::findOrFail($id);
        $shift->delete();

        return back()->with('success', 'Master Shift berhasil dihapus.');
    }
}
