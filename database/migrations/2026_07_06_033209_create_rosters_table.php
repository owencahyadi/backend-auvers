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
        Schema::create('rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('total_hours', 5, 2)->default(0);
            
            // === KOLOM BARU UNTUK KALENDER DETAIL ===
            $table->boolean('is_unavailable')->default(false);
            $table->time('start_1')->nullable();
            $table->time('end_1')->nullable();
            $table->string('role_1')->nullable();
            $table->time('start_2')->nullable();
            $table->time('end_2')->nullable();
            $table->string('role_2')->nullable();
            // ========================================

            $table->decimal('applied_rate', 8, 2)->nullable();
            $table->decimal('applied_overtime_rate', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rosters');
    }
};
