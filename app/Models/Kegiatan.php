<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = [
        'kelas_ekskul_id',
        'nama_kegiatan',
        'kategori',
        'tingkat',
        'tanggal_kegiatan'
    ];

    // public function ekskul()
    // {
    //     return $this->belongsTo(Ekskul::class);
    // }
    public function details()
    {
        return $this->hasMany(DetailKegiatan::class);
    }
    public function kelas_ekskul()
    {
        return $this->belongsTo(KelasEkskul::class, 'kelas_ekskul_id');
    }
}
