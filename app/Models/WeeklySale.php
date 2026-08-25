<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklySale extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToStore;

    protected $fillable = [
        'store_id',
        'week_start_date', 
        'food', 
        'beverage', 
        'alcohol', 
        'stall'
    ];
}