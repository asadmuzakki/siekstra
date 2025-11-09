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
            ->orderBy('created_at', 'desc') // Sort by date descending
            ->get();
        return new AbsensiResource(true, 'List of Absensi', $absensis);
    }
    /**
     * Rekap Absensi
     */
    public function rekap($ekskul_id)
    {
        $absensis = Absensi::with('details', 'kelas_ekskul.ekskul')
            ->whereHas('kelasEkskul', function ($query) use ($ekskul_id) {
                $query->where('ekskul_id', $ekskul_id); // Filter berdasarkan ekskul_id
            })
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
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
            'tanggal' => 'required|date',
            'agenda' => 'nullable|string',
            'absensis' => 'required|array',
            'absensis.*.siswa_id' => 'required|exists:siswas,id',
            'absensis.*.status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
            'absensis.*.keterangan' => 'nullable|string',
        ]);

        $absensi = Absensi::create([
            'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
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
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
            'tanggal' => 'required|date',
            'agenda' => 'nullable|string',
            'absensis' => 'required|array',
            'absensis.*.siswa_id' => 'required|exists:siswas,id',
            'absensis.*.status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
            'absensis.*.keterangan' => 'nullable|string',
        ]);

        // Update data absensi utama
        $absensi->update([
            'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
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

    public function showBySiswaId($siswaId, $tahun = null)
    {
        // Ambil absensi berdasarkan siswa_id dari relasi details
        $absensis = Absensi::whereHas('details', function ($query) use ($siswaId) {
            $query->where('siswa_id', $siswaId);
        })
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal', $tahun); // Filter berdasarkan tahun jika diberikan
            })
            ->with([
                'details' => function ($query) use ($siswaId) {
                    $query->where('siswa_id', $siswaId);
                },
                'kelas_ekskul', // Tambahkan relasi kelas_ekskul
            ])
            ->orderBy('tanggal', 'desc')
            ->get();

        if ($absensis->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Absensi Found for the Given Siswa and Year',
            ], 404);
        }

        // Tambahkan field nama_siswa dan nama_ekskul pada setiap item
        $result = $absensis->map(function ($absensi) {
            return [
                'id' => $absensi->id,
                'tanggal' => $absensi->tanggal,
                'agenda' => $absensi->agenda,
                'nama_ekskul' => $absensi->kelas_ekskul->ekskul->nama_ekskul ?? null, // Tambahkan nama kelas ekskul
                'details' => $absensi->details->map(function ($detail) {
                    return [
                        'siswa_id' => $detail->siswa_id,
                        'nama_siswa' => $detail->siswa->nama ?? null, // Tambahkan nama siswa
                        'status' => $detail->status,
                        'keterangan' => $detail->keterangan,
                    ];
                }),
            ];
        });

        return new AbsensiResource(true, 'Absensi Found for Siswa', $result);
    }

    public function indexByAbsensi(Request $request)
    {
        // Ambil parameter sorting dan filter dari query string
        $sortBy = $request->query('sort_by', 'absensi_id'); // Default: absensi_id
        $sortOrder = $request->query('sort_order', 'desc'); // Default: desc
        $tahun = $request->query('tahun'); // Filter berdasarkan tahun

        // Validasi parameter sorting
        $allowedSortBy = ['kelas', 'ekskul', 'absensi_id'];
        if (!in_array($sortBy, $allowedSortBy)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sort_by parameter. Allowed values: kelas, ekskul, absensi_id',
            ], 400);
        }

        // Ambil data dari tabel absensi_details dengan relasi siswa dan absensi
        $detailAbsensis = \App\Models\DetailAbsensi::with(['siswa', 'absensi.kelas_ekskul.ekskul'])
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereHas('absensi', function ($query) use ($tahun) {
                    $query->whereYear('tanggal', $tahun); // Filter berdasarkan tahun
                });
            })
            ->when($sortBy === 'kelas', function ($query) use ($sortOrder) {
                $query->join('siswas', 'detail_absensis.siswa_id', '=', 'siswas.id')
                    ->orderBy('siswas.kelas', $sortOrder);
            })
            ->when($sortBy === 'ekskul', function ($query) use ($sortOrder) {
                $query->join('absensis', 'detail_absensis.absensi_id', '=', 'absensis.id')
                    ->join('kelas_ekskuls', 'absensis.kelas_ekskul_id', '=', 'kelas_ekskuls.id')
                    ->join('ekskuls', 'kelas_ekskuls.ekskul_id', '=', 'ekskuls.id')
                    ->orderBy('ekskuls.nama_ekskul', $sortOrder);
            }, function ($query) use ($sortBy, $sortOrder) {
                $query->orderBy($sortBy, $sortOrder); // Default sorting
            })
            ->get();

        if ($detailAbsensis->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Absensi Found',
            ], 404);
        }

        // Format data untuk ditampilkan per absensi
        $result = $detailAbsensis->map(function ($detail) {
            return [
                'absensi_id' => $detail->absensi_id,
                'tanggal' => $detail->absensi->tanggal,
                'ekskul_id' => $detail->absensi->kelas_ekskul->ekskul->ekskul_id,
                'nama_ekskul' => $detail->absensi->kelas_ekskul->ekskul->nama_ekskul ?? null,
                'siswa_id' => $detail->siswa->id,
                'nama_siswa' => $detail->siswa->nama,
                'kelas' => $detail->siswa->kelas,
                'status' => $detail->status,
                'keterangan' => $detail->keterangan,
            ];
        });

        return new AbsensiResource(true, 'List of Absensi by Detail', $result);
    }
}
