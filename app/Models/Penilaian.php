<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'nasabah_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'array',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}
