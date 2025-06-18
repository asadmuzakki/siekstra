<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\EkskulController;
use App\Http\Controllers\Api\TesController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Selamat datang admin!']);
    });
});
Route::middleware(['auth:sanctum', 'role:tutor'])->prefix('tutor')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Selamat datang member!']);
    });
    // Route::apiResource('absensi', \App\Http\Controllers\Api\AbsensiController::class);
});

Route::post('/tes-absensi', [TesController::class, 'store']);
Route::apiResource('siswas', SiswaController::class);
Route::apiResource('ekskul', EkskulController::class);

Route::apiResource('absensi', \App\Http\Controllers\Api\AbsensiController::class);