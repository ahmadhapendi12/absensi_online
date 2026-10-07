<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CabangKantor;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = CabangKantor::all();
        return view('superadmin.cabang', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'radius_meter' => 'required|integer'
        ]);

        CabangKantor::create($request->all());
        return back()->with('success', 'Cabang baru berhasil disimpan.');
    }
}