<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'id_mhs';
    public function presensi()
    {
        return $this->hasOne(Presensi::class, 'id_mhs', 'id_mhs'); // Assuming presensi also links with id_mhs
    }

    public function kompen_mhs()
    {
        return $this->hasOne(KompenMhs::class, 'id_mhs', 'id_mhs');
    }

    public function kelas()
    {
    return $this->belongsTo(Kelas::class, 'id_kls', 'id_kls');
    }
}
