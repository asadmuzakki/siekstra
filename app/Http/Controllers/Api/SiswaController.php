<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Http\Resources\SiswaResource;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kelas = $request->query('kelas');

        $siswas = Siswa::with('pendaftarans.kelas_ekskul.ekskul')
            ->when($kelas, function ($query) use ($kelas) {
                // Gunakan LIKE agar "1" cocok dengan "1A", "1B", dst.
                $query->where('kelas', 'like', $kelas . '%');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        if ($siswas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada siswa ditemukan untuk kelas yang diminta.',
                'data' => [],
            ], 404);
        }

        return new SiswaResource(true, 'List of Siswas', $siswas);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|string|max:20|unique:siswas,nis',
            'kelas' => 'required|string|max:50',
            'nama_ortu' => 'required|string|max:100',
            'email_ortu' => 'required|email|max:100',
            'jenis_kelamin' => 'required|string|max:10', // Added jenis_kelamin validation
        ]);

        $siswa = Siswa::create($validated);

        return new SiswaResource(true, 'Siswa Created Successfully', $siswa);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return new SiswaResource(false, 'Siswa Not Found', null);
        }

        return new SiswaResource(true, 'Siswa Found', $siswa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return new SiswaResource(false, 'Siswa Not Found', null);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nis' => 'required|string|max:20|unique:siswas,nis,' . $id,
            'kelas' => 'required|string|max:50',
            'nama_ortu' => 'required|string|max:100',
            'email_ortu' => 'required|email|max:100',
            'jenis_kelamin' => 'required|string|max:10', // Added jenis_kelamin validation
        ]);

        $siswa->update($validated);

        return new SiswaResource(true, 'Siswa Updated Successfully', $siswa);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return new SiswaResource(false, 'Siswa Not Found', null);
        }

        $siswa->delete();

        return new SiswaResource(true, 'Siswa Deleted Successfully', null);
    }

    public function anakWali()
    {
        // Ambil user yang sedang login (wali murid)
        $wali = auth()->user();

        // Ambil data siswa yang memiliki email sesuai user wali
        $siswas = Siswa::with('pendaftarans.ekskul')
            ->where('email_ortu', $wali->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return new SiswaResource(true, 'List of Anak Wali Murid', $siswas);
    }
}
