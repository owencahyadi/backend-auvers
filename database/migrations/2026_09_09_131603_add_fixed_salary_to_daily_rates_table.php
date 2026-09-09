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
        Schema::table('daily_rates', function (Blueprint $table) {
            $table->boolean('is_fixed_salary')->default(false)->after('employee_id');
            $table->decimal('fixed_salary_amount', 10, 2)->nullable()->after('is_fixed_salary');
        });
    }
    public function down(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            $table->dropColumn(['is_fixed_salary', 'fixed_salary_amount']);
        });
    }
};
