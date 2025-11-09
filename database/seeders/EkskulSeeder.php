<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ekskul;
use App\Models\User;

class EkskulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Ambil semua user dengan role 'tutor'
        $tutors = User::role('tutor')->get();

        // Pastikan jumlah tutor mencukupi untuk ekskul
        if ($tutors->isEmpty()) {
            $this->command->error('Tidak ada user dengan role tutor. Jalankan RoleSeeder terlebih dahulu.');
            return;
        }

        // Data ekskul
        $ekskuls = [
            [
                'nama_ekskul' => 'Mewarnai',
                'deskripsi' => 'Ekskul seni untuk belajar mewarnai dengan teknik yang menyenangkan.',
                'jadwal' => '2025-06-20',
                'tempat' => 'Ruang Seni',
                'kelas_min' => 1,
                'kelas_max' => 2,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264189/laravel_uploads/eajxapxtgrrsbofh9mii.jpg'
            ],
            [
                'nama_ekskul' => 'Math Club',
                'deskripsi' => 'Ekskul untuk meningkatkan kemampuan matematika.',
                'jadwal' => '2025-06-21',
                'tempat' => 'Ruang Kelas',
                'kelas_min' => 1,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264158/laravel_uploads/v2xdpvx7dlmgo3iwvgfn.jpg'
            ],
            [
                'nama_ekskul' => 'Menggambar',
                'deskripsi' => 'Ekskul seni untuk belajar menggambar dengan berbagai teknik.',
                'jadwal' => '2025-06-22',
                'tempat' => 'Ruang Seni',
                'kelas_min' => 3,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264502/laravel_uploads/ldabrzmeha2imw5bs6ri.jpg'
            ],
            [
                'nama_ekskul' => 'Robotika',
                'deskripsi' => 'Ekskul teknologi untuk belajar robotika dan pemrograman.',
                'jadwal' => '2025-06-23',
                'tempat' => 'Lab Komputer',
                'kelas_min' => 1,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264235/laravel_uploads/wdnj9bx2aktursthhtr3.jpg'
            ],
            [
                'nama_ekskul' => 'Cooking Class',
                'deskripsi' => 'Ekskul memasak untuk belajar berbagai resep masakan.',
                'jadwal' => '2025-06-24',
                'tempat' => 'Dapur Sekolah',
                'kelas_min' => 4,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264257/laravel_uploads/mazrifsxbwza45wfidic.jpg'
            ],
            [
                'nama_ekskul' => 'Hadroh',
                'deskripsi' => 'Ekskul seni musik Islami untuk belajar hadroh.',
                'jadwal' => '2025-06-25',
                'tempat' => 'Ruang Musik',
                'kelas_min' => 3,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264278/laravel_uploads/j8pq0ij7f7owjt5ooz76.jpg'
            ],
            [
                'nama_ekskul' => 'Karate',
                'deskripsi' => 'Ekskul bela diri untuk belajar teknik karate.',
                'jadwal' => '2025-06-26',
                'tempat' => 'Lapangan Olahraga',
                'kelas_min' => 2,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264301/laravel_uploads/rwtxhhhtztw98yixjqrh.jpg'
            ],
            [
                'nama_ekskul' => 'Bulutangkis',
                'deskripsi' => 'Ekskul olahraga bulutangkis untuk meningkatkan kemampuan fisik.',
                'jadwal' => '2025-06-27',
                'tempat' => 'Lapangan Bulutangkis',
                'kelas_min' => 4,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264324/laravel_uploads/kqsafywqrdfamkokwj4l.jpg'
            ],
            [
                'nama_ekskul' => 'Drumband',
                'deskripsi' => 'Ekskul seni musik untuk belajar drumband.',
                'jadwal' => '2025-06-28',
                'tempat' => 'Lapangan Sekolah',
                'kelas_min' => 3,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264344/laravel_uploads/y3fwzhmqzfvq6tzedqb8.jpg'
            ],
            [
                'nama_ekskul' => 'Futsal',
                'deskripsi' => 'Ekskul olahraga futsal untuk meningkatkan kemampuan teamwork.',
                'jadwal' => '2025-06-29',
                'tempat' => 'Lapangan Futsal',
                'kelas_min' => 3,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264364/laravel_uploads/vfeuibkh9ixwxnxuaa9j.jpg'
            ],
            [
                'nama_ekskul' => 'Sains Club',
                'deskripsi' => 'Ekskul untuk belajar dan bereksperimen di bidang sains.',
                'jadwal' => '2025-06-30',
                'tempat' => 'Lab Sains',
                'kelas_min' => 1,
                'kelas_max' => 6,
                'foto_url' => 'https://res.cloudinary.com/dbgidgaiq/image/upload/v1762264134/laravel_uploads/m0carrqubz8elzgfim9m.jpg'
            ],
        ];

        // Loop ekskul dan assign tutor secara berurutan
        foreach ($ekskuls as $index => $ekskul) {
            $ekskul['tutor_id'] = $tutors[$index % $tutors->count()]->id; // Assign tutor secara berulang
            Ekskul::create($ekskul);
        }
    }
}
