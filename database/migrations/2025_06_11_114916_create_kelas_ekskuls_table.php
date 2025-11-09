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
        Schema::create('kelas_ekskuls', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->foreignId('ekskul_id')->constrained('ekskuls')->onDelete('cascade')->onUpdate('cascade');
            $table->string('tahun_ajaran');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->enum('periode', ['Ganjil', 'Genap']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas__ekskuls');
    }
};
