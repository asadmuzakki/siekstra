<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_ekskul_id')->constrained('kelas_ekskuls', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nama_kegiatan');
            $table->enum('kategori', ['lomba', 'non-lomba', 'lainnya']);
            $table->enum('tingkat', ['sekolah', 'kota', 'provinsi', 'nasional']);
            $table->date('tanggal_kegiatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
