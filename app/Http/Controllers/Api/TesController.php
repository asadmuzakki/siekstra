<?php
// app/Http/Controllers/Api/AbsensiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tes;

class TesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'agenda' => 'nullable|string',
            'absensis' => 'required|array',
            'absensis.*.siswa_id' => 'required|exists:siswas,id',
            'absensis.*.status' => 'required|in:hadir,sakit,izin,alpa',
            'absensis.*.keterangan' => 'nullable|string',
        ]);

        foreach ($request->absensis as $data) {
            Tes::create([
                'tanggal' => $request->tanggal,
                'agenda' => $request->agenda,
                'siswa_id' => $data['siswa_id'],
                'status' => $data['status'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Absensi berhasil disimpan']);
    }
}
