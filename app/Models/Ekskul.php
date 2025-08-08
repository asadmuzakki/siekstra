<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekskul extends Model
{
    protected $fillable = [
        'nama_ekskul',
        'foto_url',
        'deskripsi',
        'jadwal',
        'tempat',
        'tutor_id',
        'status'
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }
}
