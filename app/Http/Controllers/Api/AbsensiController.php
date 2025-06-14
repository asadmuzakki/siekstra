<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Http\Resources\AbsensiResource;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $absensis = Absensi::all();
        return new AbsensiResource(true, 'List of Absensi', $absensis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
        ]);

        $absensi = Absensi::create($validated);

        return new AbsensiResource(true, 'Absensi Created Successfully', $absensi);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
            return new AbsensiResource(false, 'Absensi Not Found', null);
        }

        return new AbsensiResource(true, 'Absensi Found', $absensi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
            return new AbsensiResource(false, 'Absensi Not Found', null);
        }

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
        ]);

        $absensi->update($validated);

        return new AbsensiResource(true, 'Absensi Updated Successfully', $absensi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
            return new AbsensiResource(false, 'Absensi Not Found', null);
        }

        $absensi->delete();

        return new AbsensiResource(true, 'Absensi Deleted Successfully', null);
    }
}
