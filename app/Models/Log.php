<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    public function presensis()
{
    return $this->hasMany(Presensi::class, 'id_tahun_ajar');
}
    protected $guarded = [];
}
