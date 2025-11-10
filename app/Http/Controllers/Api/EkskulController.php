<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Http\Resources\EkskulResource;
use Illuminate\Http\Request;
use App\Providers\CloudinaryService;

class EkskulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ekskuls = Ekskul::with(['tutor', 'kelas_ekskul.pedaftarans'])
            ->withCount('pendaftarans')
            ->get();
        return new EkskulResource(true, 'List of Ekskul', $ekskuls);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ekskul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'jadwal' => 'required|date',
            'tempat' => 'required|string|max:100',
            'tutor_id' => 'required|exists:users,id',
            'status' => 'required|in:aktif,nonaktif', // Assuming status can be Aktif or Nonaktif
            'foto' => 'nullable|image|max:2048', // Optional image upload
            'kelas_min' => 'required|integer|min:1|max:6',
            'kelas_max' => 'required|integer|min:1|max:6|gte:kelas_min',
        ]);
        $fotoUrl = CloudinaryService::uploadImage($request->file('foto'));
        $ekskul = Ekskul::create([
            'nama_ekskul' => $validated['nama_ekskul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jadwal' => $validated['jadwal'],
            'tempat' => $validated['tempat'],
            'tutor_id' => $validated['tutor_id'],
            'status' => $validated['status'],
            'foto_url' => $fotoUrl['secure_url'] ?? null, // Store the secure URL from Cloudinary
            'kelas_min' => $validated['kelas_min'],
            'kelas_max' => $validated['kelas_max'],
        ]);

        return new EkskulResource(true, 'Ekskul Created Successfully', $ekskul);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ekskul = Ekskul::with(['tutor', 'kelas_ekskul'])->find($id);

        if (!$ekskul) {
            return new EkskulResource(false, 'Ekskul Not Found', null);
        }

        return new EkskulResource(true, 'Ekskul Found', $ekskul);
    }
    // buat method untuk menampilkan ekskul berdasarkan user_id
    public function showByUserId($userId)
    {
        $ekskuls = Ekskul::where('tutor_id', $userId)->get();

        if ($ekskuls->isEmpty()) {
            return new EkskulResource(false, 'No Ekskul found for this user', null);
        }

        return new EkskulResource(true, 'Ekskul Found', $ekskuls);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ekskul = Ekskul::find($id);

        if (!$ekskul) {
            return new EkskulResource(false, 'Ekskul Not Found', null);
        }

        $validated = $request->validate([
            'nama_ekskul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'jadwal' => 'required|string|max:100',
            'tempat' => 'required|string|max:100',
            'tutor_id' => 'required|exists:users,id',
            'status' => 'required|in:aktif,nonaktif',
            'foto' => 'nullable|image|max:2048',
            'kelas_min' => 'required|integer|min:1|max:6',
            'kelas_max' => 'required|integer|min:1|max:6|gte:kelas_min',
        ]);

        // Default foto lama
        $fotoUrl = $ekskul->foto_url;

        // Jika ada foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama di Cloudinary
            if ($ekskul->foto_url) {
                CloudinaryService::deleteImageByUrl($ekskul->foto_url);
            }

            // Upload foto baru
            $uploadResult = CloudinaryService::uploadImage($request->file('foto'));
            $fotoUrl = $uploadResult['secure_url'] ?? $fotoUrl;
        }

        $ekskul->update([
            'nama_ekskul' => $validated['nama_ekskul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jadwal' => $validated['jadwal'],
            'tempat' => $validated['tempat'],
            'tutor_id' => $validated['tutor_id'],
            'status' => $validated['status'],
            'foto_url' => $fotoUrl,
            'kelas_min' => $validated['kelas_min'],
            'kelas_max' => $validated['kelas_max'],
        ]);

        return new EkskulResource(true, 'Ekskul Updated Successfully', $ekskul);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ekskul = Ekskul::find($id);

        if (!$ekskul) {
            return new EkskulResource(false, 'Ekskul Not Found', null);
        }
        // Hapus foto di Cloudinary jika ada
        if ($ekskul->foto_url) {
            CloudinaryService::deleteImageByUrl($ekskul->foto_url);
        }
        $ekskul->delete();

        return new EkskulResource(true, 'Ekskul Deleted Successfully', null);
    }

    public function ekskulForWali()
    {
        $wali = auth()->user();
        $siswaList = $wali->anak;

        $result = [];

        foreach ($siswaList as $siswa) {
            // Ambil angka kelas dari string (contoh: "5 Mushab bin Umair" -> 5)
            preg_match('/^\d+/', $siswa->kelas, $matches);
            $tingkat = $matches[0] ?? null;

            if ($tingkat) {
                // Cari ekskul berdasarkan tingkat kelas siswa
                $ekskuls = Ekskul::where('kelas_min', '<=', $tingkat)
                    ->where('kelas_max', '>=', $tingkat)
                    ->where('status', 'aktif') // Hanya ambil ekskul yang aktif
                    ->get()
                    ->map(function ($ekskul) use ($siswa) {
                        // Periksa apakah siswa sudah terdaftar di ekskul ini
                        $isRegistered = $ekskul->pendaftarans()->where('siswa_id', $siswa->id)->exists();

                        return [
                            'id' => $ekskul->id,
                            'nama_ekskul' => $ekskul->nama_ekskul,
                            'deskripsi' => $ekskul->deskripsi,
                            'jadwal' => $ekskul->jadwal,
                            'tempat' => $ekskul->tempat,
                            'status' => $ekskul->status,
                            'foto_url' => $ekskul->foto_url,
                            'is_registered' => $isRegistered, // Tambahkan status terdaftar
                        ];
                    });
            } else {
                $ekskuls = collect(); // Jika tingkat tidak ditemukan, kembalikan koleksi kosong
            }

            $result[] = [
                'siswa' => $siswa->nama,
                'kelas' => $siswa->kelas,
                'ekskul' => $ekskuls,
            ];
        }

        return new EkskulResource(true, 'Ekskul Found', $result);
    }

    public function showBySiswaId($siswaId)
    {
        // Cari siswa berdasarkan ID
        $siswa = \App\Models\Siswa::find($siswaId);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa Not Found',
            ], 404);
        }

        // Ambil angka kelas dari string (contoh: "5 Mushab bin Umair" -> 5)
        preg_match('/^\d+/', $siswa->kelas, $matches);
        $tingkat = $matches[0] ?? null;

        if ($tingkat) {
            // Cari ekskul berdasarkan tingkat kelas siswa
            $ekskuls = Ekskul::where('kelas_min', '<=', $tingkat)
                ->where('kelas_max', '>=', $tingkat)
                ->get();
        } else {
            $ekskuls = collect(); // Jika tingkat tidak ditemukan, kembalikan koleksi kosong
        }

        return new EkskulResource(true, 'Ekskul Found for Siswa', [
            'siswa' => $siswa->nama,
            'kelas' => $siswa->kelas,
            'ekskul' => $ekskuls,
        ]);
    }
}
