<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumahIbadah extends Model
{
    /** @use HasFactory<\Database\Factories\RumahIbadahFactory> */
    use HasFactory;

    protected $table = 'rumah_ibadah';
    protected $fillable = ['nama', 'alamat', 'latitude', 'longitude', 'radius'];
}
