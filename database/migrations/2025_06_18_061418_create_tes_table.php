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
        Schema::create('tes', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->text('agenda')->nullable();
            $table->unsignedBigInteger('siswa_id');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa']);
            $table->string('keterangan')->nullable();
            $table->timestamps();
            $table->foreign('siswa_id')
                ->references('id')->on('siswas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tes');
    }
};
