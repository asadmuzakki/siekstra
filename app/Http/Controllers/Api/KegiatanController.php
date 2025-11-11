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
        $kegiatans = Kegiatan::with('details', 'kelas_ekskul.ekskul') // Eager load the details and ekskul relationships
            ->orderBy('created_at', 'asc') // Sort by date ascending
            ->get();
        return new KegiatanResource(true, 'List of Kegiatan', $kegiatans);
    }
    /**
     * Rekap Kegiatan
     */
    public function rekap($kelas_ekskul_id)
    {
        // Ambil semua kegiatan berdasarkan kelas_ekskul_id
        $kegiatans = Kegiatan::with(['details', 'kelas_ekskul'])
            ->where('kelas_ekskul_id', $kelas_ekskul_id)
            ->get();

        // Buat rekap per kegiatan
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
            'message' => 'Rekap absensi berdasarkan kelas ekskul berhasil diambil',
            'data' => $rekap
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
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
            'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
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
        $kegiatan->load('details', 'kelas_ekskul.ekskul');
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
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
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
            'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
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

    public function showBySiswaId($siswaId, $tahun = null, $periode = null)
    {
        // Ambil kegiatan berdasarkan siswa_id dari relasi details
        $kegiatans = Kegiatan::whereHas('details', function ($query) use ($siswaId) {
            $query->where('siswa_id', $siswaId);
        })
            // 🔹 Filter tahun berdasarkan tanggal kegiatan
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal_kegiatan', $tahun);
            })
            // 🔹 Filter periode berdasarkan bulan kegiatan
            ->when($periode, function ($query) use ($periode) {
                if (strtolower($periode) === 'ganjil') {
                    // Ganjil: Juli - Desember
                    $query->whereMonth('tanggal_kegiatan', '>=', 7)
                        ->whereMonth('tanggal_kegiatan', '<=', 12);
                } elseif (strtolower($periode) === 'genap') {
                    // Genap: Januari - Juni
                    $query->whereMonth('tanggal_kegiatan', '>=', 1)
                        ->whereMonth('tanggal_kegiatan', '<=', 6);
                }
            })
            ->with([
                'details' => function ($query) use ($siswaId) {
                    $query->where('siswa_id', $siswaId);
                },
                'details.siswa',             // Ambil nama siswa
                'kelas_ekskul.ekskul',       // Ambil nama ekskul
            ])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();

        // 🔸 Jika tidak ada data ditemukan
        if ($kegiatans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Kegiatan Found for the Given Siswa, Year, or Period',
            ], 404);
        }

        // 🔧 Format hasil untuk response
        $result = $kegiatans->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->id,
                'tanggal_kegiatan' => $kegiatan->tanggal_kegiatan,
                'nama_kegiatan' => $kegiatan->nama_kegiatan ?? null,
                'deskripsi' => $kegiatan->deskripsi ?? null,
                'tempat' => $kegiatan->tempat ?? null,
                'nama_ekskul' => $kegiatan->kelas_ekskul->ekskul->nama_ekskul ?? null,
                'periode' => $kegiatan->kelas_ekskul->periode ?? null,
                'tahun_ajaran' => $kegiatan->kelas_ekskul->tahun_ajaran ?? null,
                'details' => $kegiatan->details->map(function ($detail) {
                    return [
                        'siswa_id' => $detail->siswa_id,
                        'nama_siswa' => $detail->siswa->nama ?? null,
                        'status_kehadiran' => $detail->status_kehadiran ?? null,
                        'catatan' => $detail->catatan ?? null,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan Found for Siswa',
            'data' => $result,
        ]);
    }

}
