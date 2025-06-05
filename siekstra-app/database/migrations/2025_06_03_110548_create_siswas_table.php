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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('nama', 100); // Nama siswa
            $table->string('nis', 20)->unique(); // Nomor Induk Siswa
            $table->string('kelas', 50); // Kelas siswa
            $table->string('nama_ortu', 100); // Nama orang tua siswa
            $table->string('email_ortu', 100); // Email orang tua siswa
            $table->timestamps(); // Created at & Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
