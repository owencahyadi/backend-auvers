<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('daily_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('rate_mon', 8, 2)->default(0);
            $table->decimal('rate_tue', 8, 2)->default(0);
            $table->decimal('rate_wed', 8, 2)->default(0);
            $table->decimal('rate_thu', 8, 2)->default(0);
            $table->decimal('rate_fri', 8, 2)->default(0);
            $table->decimal('rate_sat', 8, 2)->default(0);
            $table->decimal('rate_sun', 8, 2)->default(0);
            $table->decimal('overtime_rate', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_rates');
    }
};
