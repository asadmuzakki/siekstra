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
        Schema::create('absensi_tutors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('users', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('kelas_ekskul_id')->constrained('kelas_ekskuls', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->date('tanggal');
            $table->enum('status', ['Hadir','Alpha','Izin','Sakit']);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_tutors');
    }
};
