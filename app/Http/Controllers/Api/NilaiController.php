<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Http\Resources\NilaiResource;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nilais = Nilai::all();
        return new NilaiResource(true, 'List of Nilai', $nilais);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'kehadiran' => 'required|string',
            'keaktifan' => 'required|string',
            'praktik' => 'required|string',
            'index_nilai' => 'required|string',
            'keterangan' => 'nullable|string|max:255',
        ]);
        $kehadiran = (float) $validated['kehadiran'];
        $keaktifan = (float) $validated['keaktifan'];
        $praktik = (float) $validated['praktik'];
        $index_nilai = $validated['index_nilai'];
        $nilai_akhir = ($kehadiran * 0.4) + ($keaktifan * 0.3) + ($praktik * 0.3);
        if($nilai_akhir >= 94 && $nilai_akhir <= 100) {
            $index_nilai = 'A';
        } elseif($nilai_akhir >= 86 && $nilai_akhir <= 93) {
            $index_nilai = 'B';
        } elseif($nilai_akhir >= 80 && $nilai_akhir <= 85) {
            $index_nilai = 'C';
        }
        $nilai = Nilai::create([
            'siswa_id' => $validated['siswa_id'],
            'ekskul_id' => $validated['ekskul_id'],
            'kehadiran' => $validated['kehadiran'],
            'keaktifan' => $validated['keaktifan'],
            'praktik' => $validated['praktik'],
            'nilai_akhir' => (string) round($nilai_akhir, 2),
            'index_nilai' => $index_nilai,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return new NilaiResource(true, 'Nilai Created Successfully', $nilai);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $nilai = Nilai::find($id);

        if (!$nilai) {
            return new NilaiResource(false, 'Nilai Not Found', null);
        }

        return new NilaiResource(true, 'Nilai Found', $nilai);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $nilai = Nilai::find($id);

        if (!$nilai) {
            return new NilaiResource(false, 'Nilai Not Found', null);
        }

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $nilai->update($validated);

        return new NilaiResource(true, 'Nilai Updated Successfully', $nilai);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $nilai = Nilai::find($id);

        if (!$nilai) {
            return new NilaiResource(false, 'Nilai Not Found', null);
        }

        $nilai->delete();

        return new NilaiResource(true, 'Nilai Deleted Successfully', null);
    }
}
