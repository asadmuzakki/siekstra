<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'kelas_ekskul_id',
        'tanggal',
    ];
    
    // public function ekskul()
    // {
    //     return $this->belongsTo(Ekskul::class);
    // }
    public function details()
    {
        return $this->hasMany(DetailNilai::class);
    }
    public function kelas_ekskul()
    {
        return $this->belongsTo(KelasEkskul::class, 'kelas_ekskul_id');
    }
    public function detailNilai()
    {
        return $this->hasMany(DetailNilai::class);
    }
    // 🔥 Accessor virtual untuk tetap bisa pakai $pendaftaran->ekskul
    public function getEkskulAttribute()
    {
        return $this->kelas_ekskul ? $this->kelas_ekskul->ekskul : null;
    }

    // Supaya properti 'ekskul' muncul otomatis di JSON API
    protected $appends = ['ekskul'];
}
