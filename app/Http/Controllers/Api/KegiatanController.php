<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Http\Resources\KegiatanResource;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kegiatans = Kegiatan::with('details', 'ekskul') // Eager load the details and ekskul relationships
            ->orderBy('created_at', 'desc') // Sort by date ascending
            ->get();
        return new KegiatanResource(true, 'List of Kegiatan', $kegiatans);
    }
    /**
     * Rekap Kegiatan
     */
    public function rekap($ekskul_id)
    {
        $kegiatans = Kegiatan::with('details')->where('ekskul_id', $ekskul_id)->get();

        $rekap = $kegiatans->map(function ($kegiatan) {
            $total = $kegiatan->details->count();
            $hadir = $kegiatan->details->where('status', 'Hadir')->count();
            $sakit = $kegiatan->details->where('status', 'Sakit')->count();
            $izin = $kegiatan->details->where('status', 'Izin')->count();
            $alpha = $kegiatan->details->where('status', 'Alpha')->count();

            return [
                'id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'tanggal_kegiatan' => $kegiatan->tanggal_kegiatan,
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
            'nama_kegiatan' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'tingkat' => 'required|string|max:50',
            'tanggal_kegiatan' => 'required|date',
            'absensis' => 'required|array',
            'absensis.*.siswa_id' => 'required|exists:siswas,id',
            'absensis.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'absensis.*.keterangan' => 'nullable|string',
        ]);
        $kegiatan = Kegiatan::create([
            'ekskul_id' => $validated['ekskul_id'],
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'kategori' => $validated['kategori'],
            'tingkat' => $validated['tingkat'],
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
        ]);
        foreach ($validated['absensis'] as $absensiData) {
            $kegiatan->details()->create([
                'siswa_id' => $absensiData['siswa_id'],
                'status' => $absensiData['status'],
                'keterangan' => $absensiData['keterangan'] ?? null,
            ]);
        }
        $kegiatan->load('details');

        return new KegiatanResource(true, 'Kegiatan Created Successfully', $kegiatan);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kegiatan = Kegiatan::find($id);

        if (!$kegiatan) {
            return new KegiatanResource(false, 'Kegiatan Not Found', null);
        }
        $kegiatan->load('details', 'ekskul');
        return new KegiatanResource(true, 'Kegiatan Found', $kegiatan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::find($id);

        if (!$kegiatan) {
            return new KegiatanResource(false, 'Kegiatan Not Found', null);
        }
        $validated = $request->validate([
            'ekskul_id' => 'required|exists:ekskuls,id',
            'nama_kegiatan' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'tingkat' => 'required|string|max:50',
            'tanggal_kegiatan' => 'required|date',
            'absensis' => 'sometimes|array',
            'absensis.*.siswa_id' => 'sometimes|required|exists:siswas,id',
            'absensis.*.status' => 'sometimes|required|in:Hadir,Sakit,Izin,Alpha',
            'absensis.*.keterangan' => 'nullable|string',
        ]);
        $kegiatan->update([
            'ekskul_id' => $validated['ekskul_id'],
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'kategori' => $validated['kategori'],
            'tingkat' => $validated['tingkat'],
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
        ]);
        // Hapus detail absensi lama
        $kegiatan->details()->delete();
        foreach ($validated['absensis'] as $absensiData) {
            $kegiatan->details()->create([
                'siswa_id' => $absensiData['siswa_id'],
                'status' => $absensiData['status'],
                'keterangan' => $absensiData['keterangan'] ?? null,
            ]);
        }
        $kegiatan->load('details');

        return new KegiatanResource(true, 'Kegiatan Updated Successfully', $kegiatan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kegiatan = Kegiatan::find($id);

        if (!$kegiatan) {
            return new KegiatanResource(false, 'Kegiatan Not Found', null);
        }

        $kegiatan->delete();

        return new KegiatanResource(true, 'Kegiatan Deleted Successfully', null);
    }

    public function showBySiswaId($siswaId, $tahun = null)
    {
        // Cari kegiatan berdasarkan siswa_id dari relasi details
        $kegiatans = Kegiatan::whereHas('details', function ($query) use ($siswaId) {
            $query->where('siswa_id', $siswaId);
        })
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal_kegiatan', $tahun); // Filter berdasarkan tahun jika diberikan
            })
            ->with([
                'details' => function ($query) use ($siswaId) {
                    $query->where('siswa_id', $siswaId);
                },
                'details.siswa', // Tambahkan relasi siswa untuk mendapatkan kelas
                'ekskul'
            ])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();

        if ($kegiatans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Kegiatan Found for the Given Siswa and Year',
            ], 404);
        }

        // Tambahkan field nama_ekskul pada setiap item
        $result = $kegiatans->map(function ($item) {
            $data = $item->toArray();
            $data['nama_ekskul'] = $item->ekskul->nama_ekskul ?? null;
            $data['nama_siswa'] = $item->details->first()->siswa->nama ?? null;
            return $data;
        });

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan Found for Siswa',
            'data' => $result,
        ]);
    }
}
