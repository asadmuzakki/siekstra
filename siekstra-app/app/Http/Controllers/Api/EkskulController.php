<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Http\Resources\EkskulResource;
use Illuminate\Http\Request;

class EkskulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ekskuls = Ekskul::all();
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
            'jadwal' => 'required|string|max:100',
            'tempat' => 'required|string|max:100',
            'tutor_id' => 'required|exists:users,id',
        ]);

        $ekskul = Ekskul::create($validated);

        return new EkskulResource(true, 'Ekskul Created Successfully', $ekskul);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ekskul = Ekskul::find($id);

        if (!$ekskul) {
            return new EkskulResource(false, 'Ekskul Not Found', null);
        }

        return new EkskulResource(true, 'Ekskul Found', $ekskul);
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
        ]);

        $ekskul->update($validated);

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

        $ekskul->delete();

        return new EkskulResource(true, 'Ekskul Deleted Successfully', null);
    }
}
