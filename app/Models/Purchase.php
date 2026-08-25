<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToStore;

    protected $fillable = [
        'store_id',
        'purchase_date',
        'supplier_item_id',
        'quantity',
        'total_price',
    ];

    // Jembatan untuk mengambil detail barang (nama, harga, kategori) saat dipanggil
    public function supplierItem()
    {
        return $this->belongsTo(SupplierItem::class, 'supplier_item_id');
    }
}