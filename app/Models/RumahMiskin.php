<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumahMiskin extends Model
{
    /** @use HasFactory<\Database\Factories\RumahMiskinFactory> */
    use HasFactory;

    protected $table = 'rumah_miskin';
    protected $primaryKey = 'id_rumah';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id_rumah', 'alamat', 'jumlah_kk', 'jumlah_orang', 'latitude', 'longitude'];
}
