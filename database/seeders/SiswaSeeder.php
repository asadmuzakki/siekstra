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
            [
                'nama' => 'Ahmad Fauzi',
                'nis' => '1234567890',
                'kelas' => '10A',
                'nama_ortu' => 'Bapak Fauzi',
                'email_ortu' => 'fauzi@example.com',
                'jenis_kelamin' => 'Laki-laki', // Added jenis_kelamin field
            ],
            [
                'nama' => 'Siti Aminah',
                'nis' => '1234567891',
                'kelas' => '10B',
                'nama_ortu' => 'Ibu Aminah',
                'email_ortu' => 'aminah@example.com',
                'jenis_kelamin' => 'Perempuan', // Added jenis_kelamin field
            ],
            [
                'nama' => 'Budi Santoso',
                'nis' => '1234567892',
                'kelas' => '11A',
                'nama_ortu' => 'Bapak Santoso',
                'email_ortu' => 'santoso@example.com',
                'jenis_kelamin' => 'Laki-laki', // Added jenis_kelamin field
            ],
            [
                'nama' => 'Dewi Lestari',
                'nis' => '1234567893',
                'kelas' => '11B',
                'nama_ortu' => 'Ibu Lestari',
                'email_ortu' => 'lestari@example.com',
                'jenis_kelamin' => 'Perempuan', // Added jenis_kelamin field
            ],
        ]);
    }
}
