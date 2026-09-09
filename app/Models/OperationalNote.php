<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'item',
        'username',
        'password',
        'cut_off_time',
        'delivery_day',
        'minimum_order',
        'contact',
        'phone',
        'noted',
    ];
}