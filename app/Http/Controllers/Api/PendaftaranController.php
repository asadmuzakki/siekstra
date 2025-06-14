<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Http\Resources\PendaftaranResource;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendaftarans = Pendaftaran::all();
        return new PendaftaranResource(true, 'List of Pendaftaran', $pendaftarans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal_daftar' => 'required|date',
        ]);

        $pendaftaran = Pendaftaran::create($validated);

        return new PendaftaranResource(true, 'Pendaftaran Created Successfully', $pendaftaran);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pendaftaran = Pendaftaran::find($id);

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
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal_daftar' => 'required|date',
        ]);

        $pendaftaran->update($validated);

        return new PendaftaranResource(true, 'Pendaftaran Updated Successfully', $pendaftaran);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pendaftaran = Pendaftaran::find($id);

        if (!$pendaftaran) {
            return new PendaftaranResource(false, 'Pendaftaran Not Found', null);
        }

        $pendaftaran->delete();

        return new PendaftaranResource(true, 'Pendaftaran Deleted Successfully', null);
    }
}
