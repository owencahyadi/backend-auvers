<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi (Mass Assignable)
    protected $guarded = [];

    // PENTING: Model Store TIDAK BOLEH menggunakan trait BelongsToStore
    // karena ini adalah tabel induk (Master Table).
}