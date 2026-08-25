<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users (Akun Login) - Nullable agar Super Admin bisa akses semua
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'store_id')) {
                $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            }
        });

        // 2. Karyawan
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'store_id')) {
                $table->foreignId('store_id')->default(1)->constrained('stores')->onDelete('cascade');
            }
        });

        // 3. Supplier Catalog
        Schema::table('supplier_items', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_items', 'store_id')) {
                $table->foreignId('store_id')->default(1)->constrained('stores')->onDelete('cascade');
            }
        });

        // 4. Catatan Belanja
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'store_id')) {
                $table->foreignId('store_id')->default(1)->constrained('stores')->onDelete('cascade');
            }
        });

        // 5. Pendapatan Mingguan
        Schema::table('weekly_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('weekly_sales', 'store_id')) {
                $table->foreignId('store_id')->default(1)->constrained('stores')->onDelete('cascade');
            }
        });

        // 6. Biaya Operasional (OpEx)
        Schema::table('operational_costs', function (Blueprint $table) {
            if (!Schema::hasColumn('operational_costs', 'store_id')) {
                $table->foreignId('store_id')->default(1)->constrained('stores')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) { $table->dropForeign(['store_id']); $table->dropColumn('store_id'); });
        Schema::table('employees', function (Blueprint $table) { $table->dropForeign(['store_id']); $table->dropColumn('store_id'); });
        Schema::table('supplier_items', function (Blueprint $table) { $table->dropForeign(['store_id']); $table->dropColumn('store_id'); });
        Schema::table('purchases', function (Blueprint $table) { $table->dropForeign(['store_id']); $table->dropColumn('store_id'); });
        Schema::table('weekly_sales', function (Blueprint $table) { $table->dropForeign(['store_id']); $table->dropColumn('store_id'); });
        Schema::table('operational_costs', function (Blueprint $table) { $table->dropForeign(['store_id']); $table->dropColumn('store_id'); });
    }
};