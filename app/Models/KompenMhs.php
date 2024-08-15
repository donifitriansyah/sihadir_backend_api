<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KompenMhs extends Model
{
    use HasFactory;
    public function kompen_mhs(){
        return $this->belongsTo(KompenMhs::class);
    }
}
