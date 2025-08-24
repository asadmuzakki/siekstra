<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('siswas')->insert([
            // Siswa dengan orang tua yang sama (Bapak Fauzi)
            [
                'nama' => 'Ahmad Fauzi',
                'nis' => '1234567890',
                'kelas' => '2A',
                'nama_ortu' => 'Bapak Fauzi',
                'email_ortu' => 'fauzi@example.com',
                'jenis_kelamin' => 'Laki-laki',
            ],
            [
                'nama' => 'Aisyah Fauzi',
                'nis' => '1234567891',
                'kelas' => '1B',
                'nama_ortu' => 'Bapak Fauzi',
                'email_ortu' => 'fauzi@example.com',
                'jenis_kelamin' => 'Perempuan',
            ],

            // Siswa dengan orang tua yang sama (Ibu Aminah)
            [
                'nama' => 'Siti Aminah',
                'nis' => '1234567892',
                'kelas' => '3C',
                'nama_ortu' => 'Ibu Aminah',
                'email_ortu' => 'aminah@example.com',
                'jenis_kelamin' => 'Perempuan',
            ],
            [
                'nama' => 'Ali Aminah',
                'nis' => '1234567893',
                'kelas' => '4A',
                'nama_ortu' => 'Ibu Aminah',
                'email_ortu' => 'aminah@example.com',
                'jenis_kelamin' => 'Laki-laki',
            ],

            // Siswa dengan orang tua yang berbeda
            [
                'nama' => 'Budi Santoso',
                'nis' => '1234567894',
                'kelas' => '2A',
                'nama_ortu' => 'Bapak Santoso',
                'email_ortu' => 'santoso@example.com',
                'jenis_kelamin' => 'Laki-laki',
            ],
            [
                'nama' => 'Dewi Lestari',
                'nis' => '1234567895',
                'kelas' => '5B',
                'nama_ortu' => 'Ibu Lestari',
                'email_ortu' => 'lestari@example.com',
                'jenis_kelamin' => 'Perempuan',
            ],

            // Siswa dengan orang tua yang sama (Bapak Maulana)
            [
                'nama' => 'Rizky Maulana',
                'nis' => '1234567896',
                'kelas' => '3C',
                'nama_ortu' => 'Bapak Maulana',
                'email_ortu' => 'maulana@example.com',
                'jenis_kelamin' => 'Laki-laki',
            ],
            [
                'nama' => 'Fitri Maulana',
                'nis' => '1234567897',
                'kelas' => '4A',
                'nama_ortu' => 'Bapak Maulana',
                'email_ortu' => 'maulana@example.com',
                'jenis_kelamin' => 'Perempuan',
            ],
        ]);
    }
}
