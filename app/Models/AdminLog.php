<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $fillable = [
        'aktivitas',
        'waktu',
        'admin_id'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
