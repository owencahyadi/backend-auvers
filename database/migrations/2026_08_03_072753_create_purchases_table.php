<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            // Tanggal pembelian / tanggal invoice
            $table->date('purchase_date'); 
            
            // Relasi ke tabel barang supplier
            $table->foreignId('supplier_item_id')->constrained('supplier_items')->onDelete('cascade');
            
            // Jumlah barang yang dibeli (menggunakan decimal agar bisa input koma, misal: 1.5 kg)
            $table->decimal('quantity', 10, 2); 
            
            // Total harga (Otomatis: Qty * Harga Barang)
            $table->decimal('total_price', 12, 2); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};