<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiTutor;
use App\Http\Resources\AbsensiTutorResource;
use Illuminate\Http\Request;

class AbsensiTutorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $absensiTutors = AbsensiTutor::all();
        return new AbsensiTutorResource(true, 'List of Absensi Tutor', $absensiTutors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
        ]);

        $absensiTutor = AbsensiTutor::create($validated);

        return new AbsensiTutorResource(true, 'Absensi Tutor Created Successfully', $absensiTutor);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $absensiTutor = AbsensiTutor::find($id);

        if (!$absensiTutor) {
            return new AbsensiTutorResource(false, 'Absensi Tutor Not Found', null);
        }

        return new AbsensiTutorResource(true, 'Absensi Tutor Found', $absensiTutor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $absensiTutor = AbsensiTutor::find($id);

        if (!$absensiTutor) {
            return new AbsensiTutorResource(false, 'Absensi Tutor Not Found', null);
        }

        $validated = $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'ekskul_id' => 'required|exists:ekskuls,id',
            'tanggal' => 'required|date',
            'status' => 'required|string|in:Hadir,Alpha,Izin,Sakit',
        ]);

        $absensiTutor->update($validated);

        return new AbsensiTutorResource(true, 'Absensi Tutor Updated Successfully', $absensiTutor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $absensiTutor = AbsensiTutor::find($id);

        if (!$absensiTutor) {
            return new AbsensiTutorResource(false, 'Absensi Tutor Not Found', null);
        }

        $absensiTutor->delete();

        return new AbsensiTutorResource(true, 'Absensi Tutor Deleted Successfully', null);
    }
}
