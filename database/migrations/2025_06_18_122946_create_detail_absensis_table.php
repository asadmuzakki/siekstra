<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')->constrained('absensis', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('siswa_id')->constrained('siswas', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['Hadir', 'Alpha', 'Izin', 'Sakit']);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_absensis');
    }
};
