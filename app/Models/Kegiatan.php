<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = [
        'ekskul_id',
        'nama_kegiatan',
        'kategori',
        'tingkat',
        'tanggal_kegiatan'
    ];

    public function ekskul()
    {
        return $this->belongsTo(Ekskul::class);
    }
    public function details()
    {
        return $this->hasMany(DetailKegiatan::class);
    }
}
