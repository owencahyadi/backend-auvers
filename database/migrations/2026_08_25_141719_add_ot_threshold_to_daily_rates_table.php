<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            // Menambahkan kolom batas jam kerja per minggu (default 38 jika kosong)
            $table->decimal('ot_threshold', 5, 2)->default(38)->after('overtime_rate');
        });
    }

    public function down(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            $table->dropColumn('ot_threshold');
        });
    }
};