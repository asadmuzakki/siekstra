<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Ekskul;
use App\Models\Absensi;
use App\Models\DetailAbsensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // === COUNTER ATAS ===
        $totalSiswa = Siswa::count();
        $totalTutor = User::role('tutor')->count();
        $totalEkskul = Ekskul::where('status', 'aktif')->count();

        // Kehadiran minggu ini
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        // Total baris absensi minggu ini (semua siswa, semua ekskul)
        $totalAbsensi = DetailAbsensi::whereHas('absensi', function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('tanggal', [$startOfWeek, $endOfWeek]);
        })->count();

        // Total hadir minggu ini
        $totalHadir = DetailAbsensi::whereHas('absensi', function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('tanggal', [$startOfWeek, $endOfWeek]);
        })->where('status', 'Hadir')->count();

        // Hitung persentase kehadiran
        $persenHadir = $totalAbsensi > 0
            ? round(($totalHadir / $totalAbsensi) * 100, 2)
            : 0;

            
        return response()->json([
            'success' => true,
            'message' => 'Dashboard Data',
            'data' => [
                'total_siswa' => $totalSiswa,
                'total_tutor' => $totalTutor,
                'total_ekskul' => $totalEkskul,
                'kehadiran_minggu_ini' => $persenHadir,
            ]
        ]);
    }

    public function grafikPendaftaran(Request $request)
    {
        $tahun = $request->query('tahun', now()->year); // default tahun sekarang

        $data = Ekskul::with([
            'kelas_ekskuls.pendaftarans' => function ($q) use ($tahun) {
                $q->whereYear('tanggal_pendaftaran', $tahun);
            }
        ])->get()->map(function ($e) {
            return [
                'ekskul' => $e->nama_ekskul,
                'total' => $e->kelas_ekskuls()->pendaftarans()->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Grafik Pendaftaran Ekskul',
            'data' => $data,
        ]);
    }

    public function grafikKegiatan(Request $request)
    {
        $tingkat = $request->query('tingkat');   // contoh: "5"
        $kategori = $request->query('kategori');  // contoh: "Olahraga"
        $tahun = $request->query('tahun');     // contoh: 2025

        $query = Ekskul::with([
            'kelas_ekskuls.kegiatans' => function ($q) use ($tingkat, $kategori, $tahun) {
                if ($tahun) {
                    $q->whereYear('tanggal_kegiatan', $tahun);
                }
                if ($tingkat) {
                    $q->where('tingkat', $tingkat);
                }
                if ($kategori) {
                    $q->where('kategori', $kategori);
                }
            }
        ]);

        $data = $query->get()->map(function ($e) {
            return [
                'ekskul' => $e->nama_ekskul,
                'total' => $e->kelas_ekskuls->kegiatans->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Grafik Kegiatan Ekskul',
            'data' => $data,
        ]);
    }

}