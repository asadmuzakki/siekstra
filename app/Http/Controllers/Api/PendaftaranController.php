<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\KelasEkskul;
use App\Models\DetailNilai;
use App\Http\Resources\PendaftaranResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendaftarans = Pendaftaran::with('siswa', 'kelas_ekskul.ekskul')
            ->orderBy('created_at', 'desc')
            ->get();

        return new PendaftaranResource(true, 'List of Pendaftaran', $pendaftarans);
    }

    /**
     * show by ekskul_id
     */
    public function showByEkskul($ekskul_id)
    {
        $pendaftarans = Pendaftaran::whereHas('kelas_ekskul', function ($query) use ($ekskul_id) {
            $query->where('ekskul_id', $ekskul_id);
        })
            ->with('siswa', 'kelas_ekskul.ekskul')
            ->get();

        return new PendaftaranResource(true, 'List of Pendaftaran by Ekskul', $pendaftarans);
    }

    /**
     * Store a newly created resource in storage.
     * Saat siswa didaftarkan → otomatis menambah detail_nilai kosong.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
        ]);

        DB::beginTransaction();

        try {
            $kelas = KelasEkskul::with('nilai')->findOrFail($validated['kelas_ekskul_id']);

            // Jika kelas nonaktif → tolak pendaftaran
            if ($kelas->status === 'Nonaktif') {
                return new PendaftaranResource(false, 'Kelas ini sudah nonaktif, tidak bisa menerima pendaftaran.', null);
            }

            // Cegah pendaftaran ganda
            $cekPendaftaran = Pendaftaran::where('siswa_id', $validated['siswa_id'])
                ->where('kelas_ekskul_id', $validated['kelas_ekskul_id'])
                ->first();

            if ($cekPendaftaran) {
                return new PendaftaranResource(false, 'Siswa sudah terdaftar di kelas ini.', null);
            }

            // Simpan data pendaftaran
            $pendaftaran = Pendaftaran::create([
                'siswa_id' => $validated['siswa_id'],
                'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
                'tanggal_pendaftaran' => Carbon::today(),
            ]);

            // Ambil nilai kosong (record nilai pertama di kelas ini)
            $nilai = $kelas->nilai->first();
            if (!$nilai) {
                return new PendaftaranResource(false, 'Nilai dasar tidak ditemukan untuk kelas ini.', null);
            }

            // Buat detail nilai kosong untuk siswa ini
            DetailNilai::firstOrCreate([
                'nilai_id' => $nilai->id,
                'siswa_id' => $validated['siswa_id'],
            ], [
                'kehadiran' => null,
                'keaktifan' => null,
                'praktik' => null,
                'nilai_akhir' => null,
                'index_nilai' => null,
                'keterangan' => null,
            ]);

            DB::commit();

            $pendaftaran->load('kelas_ekskul.ekskul', 'siswa');

            return new PendaftaranResource(true, 'Pendaftaran Created Successfully dan detail_nilai dibuat.', $pendaftaran);
        } catch (\Throwable $e) {
            DB::rollBack();
            return new PendaftaranResource(false, $e->getMessage(), null);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pendaftaran = Pendaftaran::with('kelas_ekskul.ekskul', 'siswa')->find($id);

        if (!$pendaftaran) {
            return new PendaftaranResource(false, 'Pendaftaran Not Found', null);
        }

        return new PendaftaranResource(true, 'Pendaftaran Found', $pendaftaran);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::find($id);

        if (!$pendaftaran) {
            return new PendaftaranResource(false, 'Pendaftaran Not Found', null);
        }

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
        ]);

        $pendaftaran->update([
            'siswa_id' => $validated['siswa_id'],
            'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
            'tanggal_pendaftaran' => Carbon::today(),
            'jumlah_pindah' => $pendaftaran->jumlah_pindah + 1,
        ]);

        return new PendaftaranResource(true, 'Pendaftaran Updated Successfully', $pendaftaran);
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus juga detail_nilai terkait.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pendaftaran = Pendaftaran::find($id);

            if (!$pendaftaran) {
                return new PendaftaranResource(false, 'Pendaftaran Not Found', null);
            }

            // Hapus detail nilai untuk siswa ini
            $kelas = KelasEkskul::with('nilai')->findOrFail($pendaftaran->kelas_ekskul_id);
            $nilai = $kelas->nilai->first();

            if ($nilai) {
                DetailNilai::where('nilai_id', $nilai->id)
                    ->where('siswa_id', $pendaftaran->siswa_id)
                    ->delete();
            }

            // Hapus data pendaftaran
            $pendaftaran->delete();

            DB::commit();

            return new PendaftaranResource(true, 'Pendaftaran dan detail nilai siswa berhasil dihapus.', null);
        } catch (\Throwable $e) {
            DB::rollBack();
            return new PendaftaranResource(false, $e->getMessage(), null);
        }
    }
}
