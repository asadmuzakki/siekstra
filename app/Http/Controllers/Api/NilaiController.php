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
        $nilais = Nilai::with('details') // Eager load the details relationship
            ->orderBy('created_at', 'desc') // Sort by date descending
            ->get();
        return new NilaiResource(true, 'List of Nilai', $nilais);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'penilaians' => 'required|array',
            'penilaians.*.siswa_id' => 'required|exists:siswas,id',
            'penilaians.*.kehadiran' => 'required|string',
            'penilaians.*.keaktifan' => 'required|string',
            'penilaians.*.praktik' => 'required|string',
            'penilaians.*.keterangan' => 'nullable|string|max:255',
        ]);
        $nilai = Nilai::create([
            'ekskul_id' => $validated['ekskul_id'],
            'tanggal' => $validated['tanggal'],
        ]);
        foreach ($validated['penilaians'] as $data) {
            // Konversi sementara ke float untuk perhitungan
            $kehadiran = (float) $data['kehadiran'];
            $keaktifan = (float) $data['keaktifan'];
            $praktik = (float) $data['praktik'];

            $nilai_akhir = ($kehadiran * 0.4) + ($keaktifan * 0.3) + ($praktik * 0.3);
            if ($nilai_akhir >= 94 && $nilai_akhir <= 100) {
                $index_nilai = 'A';
            } elseif ($nilai_akhir >= 86 && $nilai_akhir <= 93) {
                $index_nilai = 'B';
            } elseif ($nilai_akhir >= 80 && $nilai_akhir <= 85) {
                $index_nilai = 'C';
            }
            // Simpan ke tabel nilai (contoh: model Nilai)
            $nilai->details()->create([
                'siswa_id' => $data['siswa_id'],
                'kehadiran' => $data['kehadiran'], // string
                'keaktifan' => $data['keaktifan'], // string
                'praktik' => $data['praktik'],   // string
                'nilai_akhir' => (string) round($nilai_akhir, 2),
                'index_nilai' => $index_nilai,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }
        // Eager load the details relationship to include in the response
        $nilai->load('details');

        return new NilaiResource(true, 'Nilai Created Successfully', $nilai);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $nilai = Nilai::with('details.siswa')->find($id);
        // $nilai->load('details.siswa'); // Eager load details relationship
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
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'penilaians' => 'required|array',
            'penilaians.*.siswa_id' => 'required|exists:siswas,id',
            'penilaians.*.kehadiran' => 'required|string',
            'penilaians.*.keaktifan' => 'required|string',
            'penilaians.*.praktik' => 'required|string',
            'penilaians.*.keterangan' => 'nullable|string|max:255',
        ]);

        // Update data utama nilai
        $nilai->update([
            'ekskul_id' => $validated['ekskul_id'],
            'tanggal' => $validated['tanggal'],
        ]);

        // Hapus detail nilai lama
        $nilai->details()->delete();

        // Simpan detail nilai baru
        foreach ($validated['penilaians'] as $data) {
            $kehadiran = (float) $data['kehadiran'];
            $keaktifan = (float) $data['keaktifan'];
            $praktik = (float) $data['praktik'];

            $nilai_akhir = ($kehadiran * 0.4) + ($keaktifan * 0.3) + ($praktik * 0.3);

            if ($nilai_akhir >= 94 && $nilai_akhir <= 100) {
                $index_nilai = 'A';
            } elseif ($nilai_akhir >= 86 && $nilai_akhir <= 93) {
                $index_nilai = 'B';
            } elseif ($nilai_akhir >= 80 && $nilai_akhir <= 85) {
                $index_nilai = 'C';
            }

            $nilai->details()->create([
                'siswa_id' => $data['siswa_id'],
                'kehadiran' => $data['kehadiran'],
                'keaktifan' => $data['keaktifan'],
                'praktik' => $data['praktik'],
                'nilai_akhir' => (string) round($nilai_akhir, 2),
                'index_nilai' => $index_nilai,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        // Muat relasi details untuk ditampilkan dalam respons
        $nilai->load('details');

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
