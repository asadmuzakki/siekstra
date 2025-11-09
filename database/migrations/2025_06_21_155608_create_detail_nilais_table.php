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
        Schema::create('detail_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_id')->constrained('nilais', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('siswa_id')->constrained('siswas', 'id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('kehadiran')->nullable();
            $table->string('keaktifan')->nullable();
            $table->string('praktik')->nullable();
            $table->string('nilai_akhir')->nullable();
            $table->string('index_nilai')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_nilais');
    }
};
