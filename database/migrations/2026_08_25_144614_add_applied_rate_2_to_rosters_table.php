<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rosters', function (Blueprint $table) {
            if (!Schema::hasColumn('rosters', 'applied_rate_2')) {
                $table->decimal('applied_rate_2', 8, 2)->nullable()->after('applied_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rosters', function (Blueprint $table) {
            if (Schema::hasColumn('rosters', 'applied_rate_2')) {
                $table->dropColumn('applied_rate_2');
            }
        });
    }
};