<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kompen_mhs extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function kompen_mhs(){
        return $this->belongsTo(Kompen_mhs::class);
    }
}
