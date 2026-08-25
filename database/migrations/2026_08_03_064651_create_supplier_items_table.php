<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_items', function (Blueprint $table) {
            $table->id();
            // Kategori (Meat, Seafood, Vege, dll)
            $table->string('category'); 
            // Nama Barang (Bacon, Chicken Bone, dll)
            $table->string('item_name'); 
            // Satuan / Ukuran Pembelian (1kg, 500gr, 1dozen)
            $table->string('measurement')->nullable(); 
            // Harga Beli 
            $table->decimal('price', 10, 2)->default(0); 
            // Harga Per Item (Untuk HPP/Recipe Costing)
            $table->decimal('price_per_item', 12, 5)->nullable(); 
            // Nama Supplier (B&E, Foodlink, dll)
            $table->string('supplier_name')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_items');
    }
};