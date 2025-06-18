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
        ]);

        Ekskul::create([
            'nama_ekskul' => 'Music Club',
            'deskripsi' => 'Ekskul musik untuk belajar alat musik dan vokal.',
            'jadwal' => '2025-06-21',
            'tempat' => 'Ruang Musik',
            'tutor_id' => 2, // ID tutor yang sudah ada di tabel users
            'status' => 'aktif',
        ]);

        Ekskul::create([
            'nama_ekskul' => 'Robotics Club',
            'deskripsi' => 'Ekskul teknologi untuk belajar robotika dan pemrograman.',
            'jadwal' => '2025-06-22',
            'tempat' => 'Lab Komputer',
            'tutor_id' => 2, // ID tutor yang sudah ada di tabel users
            'status' => 'nonaktif',
        ]);
    }
}
