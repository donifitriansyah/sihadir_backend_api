<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Specify the primary key column
    protected $primaryKey = 'id_kls';

    // Specify the type of the primary key
    protected $keyType = 'string'; // or 'int', depending on your primary key type

    // Specify whether the primary key is incrementing
    public $incrementing = false; // or true, depending on your use case

    // Specify which attributes should not be mass-assignable
    protected $guarded = [];
}
