<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekskul extends Model
{
    protected $fillable = [
        'nama_ekskul',
        'deskripsi',
        'jadwal',
        'tempat',
        'tutor_id'
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}
