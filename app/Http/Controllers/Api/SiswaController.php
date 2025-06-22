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
    public function index()
    {
        $siswas = Siswa::all();
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
}
