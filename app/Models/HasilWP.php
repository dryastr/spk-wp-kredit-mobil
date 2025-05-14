<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilWP extends Model
{
    use HasFactory;

    protected $table = 'hasil_wp';
    protected $fillable = [
        'nasabah_id',
        'vektor_s',
        'vektor_v',
        'layak'
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}
