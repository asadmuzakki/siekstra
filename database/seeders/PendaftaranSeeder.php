<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pendaftaran;

class PendaftaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // membuat siswa terdaftar di ekskul
        // Contoh data pendaftaran
        $pendaftarans = [
            [
                'siswa_id' => 1,
                'ekskul_id' => 1,
            ],
            [
                'siswa_id' => 2,
                'ekskul_id' => 1,
            ],
            [
                'siswa_id' => 1,
                'ekskul_id' => 2,
            ],
            [
                'siswa_id' => 3,
                'ekskul_id' => 3,
            ],
        ];

        foreach ($pendaftarans as $data) {
            Pendaftaran::create($data);
        }
    }
}
