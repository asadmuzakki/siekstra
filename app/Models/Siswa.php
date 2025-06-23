<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nis',
        'kelas',
        'nama_ortu',
        'email_ortu',
        'wali_murid_id'
    ];
    public function wali() {
        return $this->belongsTo(User::class, 'wali_murid_id');
    }
    public function detailNilais() {
        return $this->hasMany(DetailNilai::class);
    }
    public function pendaftarans() {
        return $this->hasMany(Pendaftaran::class);
    }
}
