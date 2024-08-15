<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function jadwal(){
        return $this->belongsTo(Jadwal::class);
    }
    public function presensis()
{
    return $this->hasMany(Presensi::class, 'id_jdwl');
}
    protected $primaryKey = 'id_jdwl';
}
