<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ekskul;

class EkskulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Ekskul::create([
            'nama_ekskul' => 'Basketball Club',
            'deskripsi' => 'Ekskul olahraga bola basket untuk meningkatkan kemampuan fisik dan teamwork.',
            'jadwal' => '2025-06-20',
            'tempat' => 'Lapangan Basket Sekolah',
            'tutor_id' => 2, // ID tutor yang sudah ada di tabel users
            'status' => 'aktif',
            'kelas_min' => 1, // Kelas minimal yang diperbolehkan
            'kelas_max' => 6, // Kelas maksimal yang diperbolehkan
        ]);

        Ekskul::create([
            'nama_ekskul' => 'Music Club',
            'deskripsi' => 'Ekskul musik untuk belajar alat musik dan vokal.',
            'jadwal' => '2025-06-21',
            'tempat' => 'Ruang Musik',
            'tutor_id' => 2, // ID tutor yang sudah ada di tabel users
            'status' => 'aktif',
            'kelas_min' => 3,
            'kelas_max' => 5,
        ]);

        Ekskul::create([
            'nama_ekskul' => 'Robotics Club',
            'deskripsi' => 'Ekskul teknologi untuk belajar robotika dan pemrograman.',
            'jadwal' => '2025-06-22',
            'tempat' => 'Lab Komputer',
            'tutor_id' => 2, // ID tutor yang sudah ada di tabel users
            'status' => 'nonaktif',
            'kelas_min' => 2,
            'kelas_max' => 4,
        ]);
    }
}
