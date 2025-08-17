<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user_id' => $user->id,
            'role' => $user->getRoleNames(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        // Cek apakah email terdaftar pada data siswa
        $siswa = \App\Models\Siswa::where('email_ortu', $request->email)->first();

        if (!$siswa) {
            return response()->json([
                'message' => 'Email not found in siswa data. Please use the email registered for wali murid.',
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if (!$user) {
            return response()->json(['message' => 'User registration failed'], 500);
        }
        $user->assignRole('wali_murid'); // Assign default role
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user_id' => $user->id,
            'role' => $user->getRoleNames(),
        ]);
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user(); // Mendapatkan pengguna yang sedang login

        // Validasi input
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update data pengguna
        if ($request->has('name')) {
            $user->name = $validated['name'];
        }

        if ($request->has('email')) {
            $user->email = $validated['email'];
        }

        if ($request->has('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function getProfile()
    {
        $user = Auth::user(); // Mendapatkan pengguna yang sedang login

        return response()->json([
            'message' => 'Profile retrieved successfully',
            'user' => $user,
        ]);
    }
}
