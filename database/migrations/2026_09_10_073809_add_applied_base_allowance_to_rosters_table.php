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
        Schema::table('rosters', function (Blueprint $table) {
            $table->decimal('applied_base_allowance', 10, 2)->nullable()->after('applied_rate_2');
        });
    }
    public function down(): void
    {
        Schema::table('rosters', function (Blueprint $table) {
            $table->dropColumn('applied_base_allowance');
        });
    }
};
