<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $fillable = [
        'siswa_id',
        'kelas_ekskul_id',
        'tanggal_pendaftaran',
        'jumlah_pindah',
    ];

    protected static function boot()
    {
        parent::boot();

        // Event saat data pendaftaran dibuat
        static::created(function ($pendaftaran) {
            $kelas = $pendaftaran->kelas_ekskul()->with('nilai')->first();

            if (!$kelas || $kelas->status === 'Nonaktif')
                return;

            $nilai = $kelas->nilai->first();
            if (!$nilai)
                return;

            // Cegah duplikasi
            $cek = DetailNilai::where('nilai_id', $nilai->id)
                ->where('siswa_id', $pendaftaran->siswa_id)
                ->first();

            if (!$cek) {
                DetailNilai::create([
                    'nilai_id' => $nilai->id,
                    'siswa_id' => $pendaftaran->siswa_id,
                    'kehadiran' => null,
                    'keaktifan' => null,
                    'praktik' => null,
                    'nilai_akhir' => null,
                    'index_nilai' => null,
                    'keterangan' => null,
                ]);
            }
        });
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // public function ekskul()
    // {
    //     return $this->belongsTo(Ekskul::class);
    // }
    public function kelas_ekskul()
    {
        return $this->belongsTo(KelasEkskul::class, 'kelas_ekskul_id');
    }
    // 🔥 Accessor virtual untuk tetap bisa pakai $pendaftaran->ekskul
    public function getEkskulAttribute()
    {
        return $this->kelas_ekskul ? $this->kelas_ekskul->ekskul : null;
    }

    // Supaya properti 'ekskul' muncul otomatis di JSON API
    protected $appends = ['ekskul'];
}
