<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    use HasFactory;

    // Mengizinkan 3 kolom ini untuk diisi dari form React
    protected $fillable = [
        'employee_id',
        'date',
        'total_hours',
        'applied_rate',
        'applied_overtime_rate',
        
        // --- KOLOM BARU YANG KITA TAMBAHKAN ---
        'is_unavailable',
        'start_1',
        'end_1',
        'role_1',
        'start_2',
        'end_2',
        'role_2',
    ];
}