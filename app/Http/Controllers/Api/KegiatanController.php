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
        $kegiatans = Kegiatan::all();
        return new KegiatanResource(true, 'List of Kegiatan', $kegiatans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'nama_kegiatan' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'tingkat' => 'required|string|max:50',
            'tanggal_kegiatan' => 'required|date',
        ]);

        $kegiatan = Kegiatan::create($validated);

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
            'siswa_id' => 'required|exists:siswas,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'nama_kegiatan' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'tingkat' => 'required|string|max:50',
            'tanggal_kegiatan' => 'required|date',
        ]);

        $kegiatan->update($validated);

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
}
