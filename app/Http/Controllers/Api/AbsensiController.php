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
        $absensis->load('details');
        return new AbsensiResource(true, 'List of Absensi', $absensis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'agenda' => 'required|string',
            'absensis' => 'required|array',
            'absensis.*.siswa_id' => 'required|exists:siswas,id',
            'absensis.*.status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
            'absensis.*.keterangan' => 'nullable|string',
        ]);

        $absensi = Absensi::create([
            'ekskul_id' => $validated['ekskul_id'],
            'tanggal' => $validated['tanggal'],
            'agenda' => $validated['agenda'] ?? null,
        ]);
        foreach ($validated['absensis'] as $absensiData) {
            $absensi->details()->create([
                'siswa_id' => $absensiData['siswa_id'],
                'status' => $absensiData['status'],
                'keterangan' => $absensiData['keterangan'] ?? null,
            ]);
        }
        $absensi->load('details');
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
