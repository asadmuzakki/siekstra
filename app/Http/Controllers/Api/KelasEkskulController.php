<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KelasEkskul;
use App\Models\Ekskul;
use App\Models\Nilai;
use App\Models\DetailNilai;
use App\Models\Siswa;
use App\Http\Resources\KelasEkskulResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasEkskulController extends Controller
{
    /**
     * Menampilkan semua kelas ekskul.
     */
    public function index(Request $request)
    {
        $tahun = $request->query('tahun');   // contoh: 2025
        $periode = $request->query('periode'); // contoh: Ganjil / Genap

        $kelas = KelasEkskul::with(['ekskul', 'nilai.details.siswa'])
            ->when($tahun, function ($query) use ($tahun) {
                $query->where('tahun_ajaran', $tahun);
            })
            ->when($periode, function ($query) use ($periode) {
                $query->where('periode', $periode);
            })
            ->get();

        return new KelasEkskulResource(true, 'List of Kelas Ekskul', $kelas);
    }

    /**
     * Menampilkan satu kelas ekskul dengan nilai dan detail nilai.
     */
    public function show($id)
    {
        $kelas = KelasEkskul::with('ekskul', 'nilai.detailNilai.siswa')->findOrFail($id);
        return new KelasEkskulResource(true, 'Detail Kelas Ekskul', $kelas);
    }

    /**
     * Menyimpan kelas ekskul baru dan otomatis membuat nilai kosong.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ekskul_id' => 'required|exists:ekskuls,id',
            'nama_kelas' => 'required|string|max:100',
            'tahun_ajaran' => 'required|string|max:20',
            'periode' => 'required|in:Ganjil,Genap',
            'status' => 'in:Aktif,Nonaktif'
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Buat kelas ekskul baru
            $kelas = KelasEkskul::create([
                'ekskul_id' => $validated['ekskul_id'],
                'nama_kelas' => $validated['nama_kelas'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'periode' => $validated['periode'],
                'status' => $validated['status'] ?? 'Aktif',
            ]);

            // 2️⃣ Buat nilai kosong otomatis
            $nilai = Nilai::create([
                'kelas_ekskul_id' => $kelas->id,
                'tanggal' => now(),
            ]);

            DB::commit();

            return new KelasEkskulResource(true, 'Kelas ekskul berhasil dibuat dan nilai kosong telah ditambahkan.', [$kelas, $nilai]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return new KelasEkskulResource(false, $e->getMessage(), null);
        }
    }

    /**
     * Update data kelas ekskul.
     */
    public function update(Request $request, $id)
    {
        $kelas = KelasEkskul::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas' => 'sometimes|string|max:100',
            'tahun_ajaran' => 'sometimes|string|max:20',
            'periode' => 'sometimes|in:Ganjil,Genap',
            'status' => 'sometimes|in:Aktif,Nonaktif'
        ]);

        $kelas->update($validated);

        return new KelasEkskulResource(true, 'Kelas ekskul berhasil diupdate.', $kelas);
    }

    /**
     * Hapus kelas ekskul, otomatis hapus nilai dan detail_nilai.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $kelas = KelasEkskul::with('nilai.detailNilai')->findOrFail($id);

            // Hapus semua nilai dan detail nilai
            foreach ($kelas->nilai as $nilai) {
                DetailNilai::where('nilai_id', $nilai->id)->delete();
                $nilai->delete();
            }

            $kelas->delete();

            DB::commit();

            return new KelasEkskulResource(true, 'Kelas ekskul dan nilai terkait berhasil dihapus.', null);
        } catch (\Throwable $e) {
            DB::rollBack();
            return new KelasEkskulResource(false, $e->getMessage(), null);
        }
    }
    public function getKelasByIdEkskul(Request $request, $ekskulId)
    {
        try {
            // Ambil query params dari request (opsional)
            $periodeFilter = $request->query('periode');
            $tahunFilter = $request->query('tahun');

            // Tentukan periode aktif berdasarkan bulan saat ini
            $bulanSekarang = now()->month;
            $periodeAktif = $bulanSekarang >= 7 ? 'Ganjil' : 'Genap';
            $tahunSekarang = now()->year;

            // Jika tidak ada query, gunakan default (tahun & periode aktif)
            $periode = $periodeFilter ?? $periodeAktif;
            $tahun = $tahunFilter ?? $tahunSekarang;

            // Ambil data kelas ekskul sesuai ekskul_id + filter tahun & periode
            $kelas = KelasEkskul::where('ekskul_id', $ekskulId)
                ->where('periode', $periode)
                ->where(function ($query) use ($tahun) {
                    // Handle tahun ajaran berbentuk "2025/2026"
                    $query->where('tahun_ajaran', 'like', "%$tahun%");
                })
                ->get();

            // Jika tidak ada hasil
            if ($kelas->isEmpty()) {
                return new KelasEkskulResource(false, "Tidak ada kelas ekskul untuk tahun $tahun dan periode $periode.", null);
            }

            return new KelasEkskulResource(true, "Kelas ekskul tahun $tahun periode $periode retrieved successfully.", $kelas);
        } catch (\Throwable $e) {
            return new KelasEkskulResource(false, $e->getMessage(), null);
        }
    }

}
