<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierItem extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToStore;

    protected $fillable = [
        'store_id',
        'category',
        'item_name',
        'measurement',
        'price',
        'price_per_item',
        'supplier_name'
    ];
}