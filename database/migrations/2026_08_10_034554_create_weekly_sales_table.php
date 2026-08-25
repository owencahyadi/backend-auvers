<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weekly_sales', function (Blueprint $table) {
            $table->id();
            // Menyimpan hari Senin di minggu tersebut
            $table->date('week_start_date')->unique(); 
            $table->decimal('food', 12, 2)->default(0);
            $table->decimal('beverage', 12, 2)->default(0);
            $table->decimal('alcohol', 12, 2)->default(0);
            $table->decimal('stall', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_sales');
    }
};