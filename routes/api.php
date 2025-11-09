<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\EkskulController;
use App\Http\Controllers\Api\KelasEkskulController;
use App\Http\Controllers\Api\TesController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\KegiatanController;
use App\Http\Controllers\Api\AbsensiTutorController;
use App\Http\Controllers\Api\NilaiController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::put('/edit-profile', [AuthController::class, 'editProfile']);
    Route::patch('/edit-profile', [AuthController::class, 'editProfile']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/grafik-pendaftaran', [DashboardController::class, 'grafikPendaftaran']);
    Route::get('/dashboard/grafik-kegiatan', [DashboardController::class, 'grafikKegiatan']);
    Route::apiResource('siswas', SiswaController::class);
    Route::post('/add-tutor', [UserController::class, 'addTutor']);
    Route::delete('/delete-tutor/{id}', [UserController::class, 'deleteTutor']);
    Route::put('/update-tutor/{id}', [UserController::class, 'updateTutor']);
    Route::patch('/update-tutor/{id}', [UserController::class, 'updateTutor']);
    Route::get('/getTutor/{id}', [UserController::class, 'getTutor']);
    Route::get('/getTutors', [UserController::class, 'getTutors']);
    Route::post('/add-wali-murid', [UserController::class, 'addWaliMurid']);
    Route::get('/getWaliMurid/{id}', [UserController::class, 'getWaliMurid']);
    Route::get('/getWaliMurids', [UserController::class, 'getWaliMurids']);
    Route::put('/update-wali-murid/{id}', [UserController::class, 'updateWaliMurid']);
    Route::patch('/update-wali-murid/{id}', [UserController::class, 'updateWaliMurid']);
    Route::delete('/delete-wali-murid/{id}', [UserController::class, 'deleteWaliMurid']);
    Route::apiResource('ekskul', EkskulController::class);
    Route::apiResource('absensi-tutor', AbsensiTutorController::class);
    Route::get('/nilaiByDetail', [NilaiController::class, 'indexByNilai']);
    Route::get('/absensiByDetail', [AbsensiController::class, 'indexByAbsensi']);
    Route::apiResource('pendaftaran', \App\Http\Controllers\Api\PendaftaranController::class);
    Route::prefix('kelas-ekskul')->group(function () {
        Route::get('/', [KelasEkskulController::class, 'index']);
        Route::get('/{id}', [KelasEkskulController::class, 'show']);
        Route::post('/', [KelasEkskulController::class, 'store']);
        Route::put('/{id}', [KelasEkskulController::class, 'update']);
        Route::delete('/{id}', [KelasEkskulController::class, 'destroy']);
    });
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
    Route::apiResource('ekskul', EkskulController::class);
});
Route::middleware(['auth:sanctum', 'role:wali_murid'])->prefix('wali_murid')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Selamat datang siswa!']);
    });
    Route::apiResource('pendaftaran', \App\Http\Controllers\Api\PendaftaranController::class);
    Route::get('/pendaftaranBySiswa/{siswa_id}', [\App\Http\Controllers\Api\PendaftaranController::class, 'showBySiswa']);
    Route::get('/ekskulForWali', [EkskulController::class, 'ekskulForWali']);
    Route::get('/ekskulBySiswa/{siswa_id}', [EkskulController::class, 'showBySiswaId']);
    Route::get('/absensiBySiswa/{siswa_id}/{tahun?}', [AbsensiController::class, 'showBySiswaId']);
    Route::get('/kegiatanBySiswa/{siswa_id}/{tahun?}', [KegiatanController::class, 'showBySiswaId']);
    Route::get('/nilaiBySiswa/{siswa_id}/{tahun?}', [NilaiController::class, 'showBySiswaId']);
    Route::get('/anak-wali', [SiswaController::class, 'anakWali']);
    Route::get('/getEkskul', [EkskulController::class, 'index']);
});

Route::post('/tes-absensi', [TesController::class, 'store']);
Route::get('/nilaiByDetail', [NilaiController::class, 'indexByNilai']);
Route::get('/absensiByDetail', [AbsensiController::class, 'indexByAbsensi']);
