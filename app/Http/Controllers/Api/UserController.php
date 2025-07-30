<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    // Method untuk menambah tutor by admin
    public function addTutor(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat user baru dengan role tutor
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role tutor
        $user->assignRole('tutor');

        return response()->json([
            'message' => 'Tutor created successfully',
            'user' => $user,
        ], 201);
    }
    // Method untuk menghapus tutor by admin
    public function deleteTutor($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    // Method untuk mengupdate tutor by admin
    public function updateTutor(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validasi input
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        // Update user data
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }
    // Method untuk mendapatkan daftar tutor
    public function getTutors()
    {
        $tutors = User::role('tutor')->get();
        return response()->json(['tutors' => $tutors], 200);
    }
    // Method untuk mendapatkan detail tutor
    public function getTutor($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }
    // Method untuk menambah wali murid by admin
    public function addWaliMurid(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        // Cek apakah email terdaftar pada data siswa
        $siswa = \App\Models\Siswa::where('email_ortu', $request->email)->first();
        if (!$siswa) {
            return response()->json(['message' => 'Email not found in siswa data. Please use the email registered for wali murid.'], 404);
        }
        // Buat user baru dengan role wali_murid
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role wali_murid
        $user->assignRole('wali_murid');

        return response()->json([
            'message' => 'Wali murid created successfully',
            'user' => $user,
        ], 201);
    }
    // Method untuk menghapus wali murid by admin
    public function deleteWaliMurid($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    // Method untuk mengupdate wali murid by admin
    public function updateWaliMurid(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validasi input
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        // Update user data
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }
    // Method untuk mendapatkan daftar wali murid
    public function getWaliMurids()
    {
        $waliMurids = User::role('wali_murid')->get();
        $total_anak = Siswa::where('email_ortu', $waliMurids->pluck('email'))->count();
        return response()->json(['wali_murids' => $waliMurids, 'total_anak' => $total_anak], 200);
    }
    // Method untuk mendapatkan detail wali murid
    public function getWaliMurid($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

}
