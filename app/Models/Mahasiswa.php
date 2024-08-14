<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_mhs'; // If using id_mhs as the primary key
    protected $guarded = [];

    public function presensi()
    {
        return $this->hasOne(Presensi::class, 'id_mhs', 'id_mhs'); // Assuming presensi also links with id_mhs
    }

    public function kompen_mhs()
    {
        return $this->hasOne(Kompen_mhs::class, 'id_mhs', 'id_mhs');
    }

    public function kelas()
    {
    return $this->belongsTo(Kelas::class, 'id_kls', 'id_kls');
    }
}

