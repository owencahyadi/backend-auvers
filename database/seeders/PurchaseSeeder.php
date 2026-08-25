<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\SupplierItem;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan tabel purchases terlebih dahulu
        DB::statement('TRUNCATE TABLE purchases RESTART IDENTITY CASCADE;');

        // Ambil beberapa item dari katalog supplier
        $bacon = SupplierItem::where('item_name', 'Bacon')->first();
        $salmon = SupplierItem::where('item_name', 'Salmon Fillet S/On Tasi Sashimi')->first();
        $avocado = SupplierItem::where('item_name', 'Avocado')->first();
        $butter = SupplierItem::where('item_name', 'Butter')->first();
        $croissant = SupplierItem::where('item_name', 'Croissant')->first();

        $purchases = [];

        // Simulasi belanja di rentang tanggal Juni 2026 (sesuai periode P&L kita)
        if ($bacon) {
            $purchases[] = [
                'purchase_date' => '2026-06-23',
                'supplier_item_id' => $bacon->id,
                'quantity' => 10, // 10 kg
                'total_price' => 10 * $bacon->price,
            ];
        }

        if ($salmon) {
            $purchases[] = [
                'purchase_date' => '2026-06-24',
                'supplier_item_id' => $salmon->id,
                'quantity' => 5, // 5 kg
                'total_price' => 5 * $salmon->price,
            ];
        }

        if ($avocado) {
            $purchases[] = [
                'purchase_date' => '2026-06-25',
                'supplier_item_id' => $avocado->id,
                'quantity' => 2, // 2 crate/10kg
                'total_price' => 2 * $avocado->price,
            ];
        }

        if ($butter) {
            $purchases[] = [
                'purchase_date' => '2026-06-25',
                'supplier_item_id' => $butter->id,
                'quantity' => 4, // 4 kg
                'total_price' => 4 * $butter->price,
            ];
        }

        if ($croissant) {
            $purchases[] = [
                'purchase_date' => '2026-06-26',
                'supplier_item_id' => $croissant->id,
                'quantity' => 3, // 3 box
                'total_price' => 3 * $croissant->price,
            ];
        }

        foreach ($purchases as $p) {
            Purchase::create($p);
        }
    }
}