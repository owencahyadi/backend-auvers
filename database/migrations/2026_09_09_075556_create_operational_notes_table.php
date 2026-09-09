<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('operational_notes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
        $table->string('item');
        $table->string('username')->nullable();
        $table->string('password')->nullable();
        $table->string('cut_off_time')->nullable();
        $table->string('delivery_day')->nullable();
        $table->string('minimum_order')->nullable();
        $table->string('contact')->nullable();
        $table->string('phone')->nullable();
        $table->text('noted')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_notes');
    }
};
