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
        $absensis = Absensi::with('details') // Eager load the details relationship
            ->orderBy('created_at', 'asc') // Sort by date descending
            ->get();
        // $absensis->load('details');
        return new AbsensiResource(true, 'List of Absensi', $absensis);
    }
    /**
     * Rekap Absensi
     */
    public function rekap($ekskul_id)
    {
        $absensis = Absensi::with('details')
        ->where('ekskul_id', $ekskul_id)
        ->get();

        $rekap = $absensis->map(function ($absensi) {
            $total = $absensi->details->count();
            $hadir = $absensi->details->where('status', 'Hadir')->count();
            $sakit = $absensi->details->where('status', 'Sakit')->count();
            $izin = $absensi->details->where('status', 'Izin')->count();
            $alpha = $absensi->details->where('status', 'Alpha')->count();

            return [
                'id' => $absensi->id,
                'agenda' => $absensi->agenda,
                'tanggal' => $absensi->tanggal,
                'jumlah_siswa' => $total,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Rekap absensi berhasil diambil',
            'data' => $rekap
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'agenda' => 'nullable|string',
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
        $absensi->load('details');
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
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'agenda' => 'nullable|string',
            'absensis' => 'required|array',
            'absensis.*.siswa_id' => 'required|exists:siswas,id',
            'absensis.*.status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
            'absensis.*.keterangan' => 'nullable|string',
        ]);

        // Update data absensi utama
        $absensi->update([
            'ekskul_id' => $validated['ekskul_id'],
            'tanggal' => $validated['tanggal'],
            'agenda' => $validated['agenda'] ?? null,
        ]);

        // Hapus detail absensi lama
        $absensi->details()->delete();

        // Simpan detail absensi baru
        foreach ($validated['absensis'] as $absensiData) {
            $absensi->details()->create([
                'siswa_id' => $absensiData['siswa_id'],
                'status' => $absensiData['status'],
                'keterangan' => $absensiData['keterangan'] ?? null,
            ]);
        }

        // Muat relasi details untuk ditampilkan dalam respons
        $absensi->load('details');

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
