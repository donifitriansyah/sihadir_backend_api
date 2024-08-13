<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_mk';
    protected $fillable = [
        'kd_mk',
        'nama',
        'smt',
        'sks'
    ];
    protected $guarded = [];
}
