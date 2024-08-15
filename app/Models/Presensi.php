<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function Presensi(){
        return $this->belongsTo( Presensi::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kls', 'id_kls');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jdwl', 'id_jdwl');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(Log::class, 'id_tahun_ajar', 'id_tahun_ajar');
    }

    protected $primaryKey = 'id_presensi';
}
