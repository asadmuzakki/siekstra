<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\EkskulController;
use App\Http\Controllers\Api\TesController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\KegiatanController;
use App\Http\Controllers\Api\AbsensiTutorController;
use App\Http\Controllers\Api\NilaiController;
use App\Http\Controllers\Api\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Selamat datang admin!']);
    });
    Route::apiResource('siswas', SiswaController::class);
    Route::post('/add-tutor', [UserController::class, 'addTutor']);
    Route::delete('/delete-tutor/{id}', [UserController::class, 'deleteTutor']);
    Route::put('/update-tutor/{id}', [UserController::class, 'updateTutor']);
    Route::get('/getTutor/{id}', [UserController::class, 'getTutor']);
    Route::get('/getTutors', [UserController::class, 'getTutors']);
    Route::post('/add-wali-murid', [UserController::class, 'addWaliMurid']);
    Route::get('/getWaliMurid/{id}', [UserController::class, 'getWaliMurid']);
    Route::get('/getWaliMurids', [UserController::class, 'getWaliMurids']);
    Route::put('/update-wali-murid/{id}', [UserController::class, 'updateWaliMurid']);
    Route::delete('/delete-wali-murid/{id}', [UserController::class, 'deleteWaliMurid']);
    Route::apiResource('ekskul', EkskulController::class);
});
Route::middleware(['auth:sanctum', 'role:tutor'])->prefix('tutor')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Selamat datang member!']);
    });
    Route::apiResource('absensi', AbsensiController::class);
    Route::get('/riwayat-absensi/{ekskul_id}', [AbsensiController::class, 'rekap']);
    Route::get('/riwayat-kegiatan/{ekskul_id}', [KegiatanController::class, 'rekap']);
    Route::apiResource('absensi-tutor', AbsensiTutorController::class);
    Route::apiResource('nilais', NilaiController::class);
    Route::get('/nilaiByEkskul/{ekskul_id}/{total_page}', [NilaiController::class, 'showByEkskul']);
    Route::apiResource('kegiatan', KegiatanController::class);
    Route::apiResource('pendaftaran', \App\Http\Controllers\Api\PendaftaranController::class);
    Route::get('/pendaftaranByEkskul/{ekskul_id}', [\App\Http\Controllers\Api\PendaftaranController::class, 'showByEkskul']);
    Route::apiResource('siswas', SiswaController::class);
    Route::get('/ekskulByUser/{userId}', [EkskulController::class, 'showByUserId']);
    // Route::apiResource('absensi', \App\Http\Controllers\Api\AbsensiController::class);
});

Route::post('/tes-absensi', [TesController::class, 'store']);
Route::apiResource('ekskul', EkskulController::class);
