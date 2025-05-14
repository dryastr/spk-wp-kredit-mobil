<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'alamat'
    ];

    public function penilaian()
    {
        return $this->hasOne(Penilaian::class);
    }

    public function hasilWP()
    {
        return $this->hasOne(HasilWP::class);
    }
}
