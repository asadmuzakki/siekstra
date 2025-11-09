<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas_Ekskul;
use App\Models\Ekskul;
use App\Models\Nilai;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Pastikan ada ekskul terlebih dahulu
            $ekskuls = Ekskul::all();

            if ($ekskuls->count() === 0) {
                $this->command->warn('⚠️ Tidak ada data ekskul. Silakan jalankan EkskulSeeder dulu.');
                return;
            }

            foreach ($ekskuls as $ekskul) {
                // Tambahkan contoh 2 kelas per ekskul
                for ($i = 1; $i <= 2; $i++) {
                    $kelas = Kelas_Ekskul::create([
                        'nama_kelas' => "Kelas {$i} {$ekskul->nama_ekskul}",
                        'ekskul_id' => $ekskul->id,
                        'tahun_ajaran' => '2025/2026',
                        'periode' => $i % 2 == 0 ? 'Genap' : 'Ganjil',
                        'status' => 'Aktif',
                    ]);

                    // Buat nilai kosong otomatis
                    Nilai::create([
                        'kelas_ekskul_id' => $kelas->id,
                        'tanggal' => now(),
                    ]);
                }
            }

            $this->command->info('✅ Seeder KelasEkskul dan Nilai kosong berhasil dibuat.');
        });
    }
}
