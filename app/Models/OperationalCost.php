<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OperationalCost extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToStore; 
    
    protected $guarded = [];
}
