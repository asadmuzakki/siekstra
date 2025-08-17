<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pendaftaran;
use Illuminate\Support\Carbon;

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
                'tanggal_pendaftaran' => Carbon::today()
            ],
            [
                'siswa_id' => 2,
                'ekskul_id' => 1,
                'tanggal_pendaftaran' => Carbon::today()
            ],
            [
                'siswa_id' => 1,
                'ekskul_id' => 2,
                'tanggal_pendaftaran' => Carbon::today()
            ],
            [
                'siswa_id' => 3,
                'ekskul_id' => 3,
                'tanggal_pendaftaran' => Carbon::today()
            ],
        ];

        foreach ($pendaftarans as $data) {
            Pendaftaran::create($data);
        }
    }
}
