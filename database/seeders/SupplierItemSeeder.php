<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupplierItem;
use Illuminate\Support\Facades\DB;

class SupplierItemSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan tabel sebelum diisi ulang
        DB::statement('TRUNCATE TABLE supplier_items RESTART IDENTITY CASCADE;');

        $items = [
            // --- MEAT ---
            ['category' => 'Meat', 'item_name' => 'Bacon', 'measurement' => '1kg', 'price' => 13.0, 'price_per_item' => 0.0130, 'supplier_name' => 'B&E'],
            ['category' => 'Meat', 'item_name' => 'Chicken Bone', 'measurement' => '1kg', 'price' => 1.2, 'price_per_item' => 1.2000, 'supplier_name' => 'B&E'],
            ['category' => 'Meat', 'item_name' => 'Chicken Feet', 'measurement' => '1kg', 'price' => 5.0, 'price_per_item' => 0.0050, 'supplier_name' => 'B&E'],
            ['category' => 'Meat', 'item_name' => 'Chicken No10', 'measurement' => '1ea', 'price' => 6.9, 'price_per_item' => null, 'supplier_name' => 'B&E'],
            ['category' => 'Meat', 'item_name' => 'Chicken Thigh 160gr', 'measurement' => '1kg', 'price' => 14.5, 'price_per_item' => 0.0145, 'supplier_name' => 'B&E'],

            // --- SEAFOOD ---
            ['category' => 'Seafood', 'item_name' => 'Barrramundi Trimming', 'measurement' => '1kg', 'price' => 6.90, 'price_per_item' => 0.006900, 'supplier_name' => 'Foodlink'],
            ['category' => 'Seafood', 'item_name' => 'Oyster Large, Tasmanian Fresh', 'measurement' => '1dozen', 'price' => 27.00, 'price_per_item' => 2.250000, 'supplier_name' => 'Tuyen Fresh Seafood'],
            ['category' => 'Seafood', 'item_name' => 'Prawn, Cooked Tiger', 'measurement' => '1kg', 'price' => 28.00, 'price_per_item' => null, 'supplier_name' => 'Tuyen Fresh Seafood'],
            ['category' => 'Seafood', 'item_name' => 'Salmon Fillet S/On Tasi Sashimi', 'measurement' => '1kg', 'price' => 37.50, 'price_per_item' => 0.037500, 'supplier_name' => 'Foodlink'],
            ['category' => 'Seafood', 'item_name' => 'Salmon Portion Traded Skin On', 'measurement' => '140gr', 'price' => 8.25, 'price_per_item' => 0.058929, 'supplier_name' => 'PFD'],

            // --- VEGE ---
            ['category' => 'Vege', 'item_name' => 'Apple, Green Premium', 'measurement' => '1kg', 'price' => 3.8, 'price_per_item' => 0.003800, 'supplier_name' => 'ProBros Providore'],
            ['category' => 'Vege', 'item_name' => 'Avocado', 'measurement' => '10kg', 'price' => 45.8, 'price_per_item' => 0.005388, 'supplier_name' => 'ProBros Providore'],
            ['category' => 'Vege', 'item_name' => 'Blackberry', 'measurement' => '1punnet', 'price' => 5.0, 'price_per_item' => 0.040000, 'supplier_name' => 'ProBros Providore'],
            ['category' => 'Vege', 'item_name' => 'Blueberry', 'measurement' => '1punnet', 'price' => 6.5, 'price_per_item' => 0.052000, 'supplier_name' => 'ProBros Providore'],
            ['category' => 'Vege', 'item_name' => 'Broccoli, Chinese', 'measurement' => '1bunch', 'price' => 2.2, 'price_per_item' => null, 'supplier_name' => 'ProBros Providore'],

            // --- DAIRY ---
            ['category' => 'Dairy', 'item_name' => 'Butter', 'measurement' => '1kg', 'price' => 14.85, 'price_per_item' => 0.01485, 'supplier_name' => 'B&E'],
            ['category' => 'Dairy', 'item_name' => 'Butter, Unsalted', 'measurement' => '1kg', 'price' => 16.25, 'price_per_item' => 0.01625, 'supplier_name' => 'PFD'],
            ['category' => 'Dairy', 'item_name' => 'Cheese, Anita Sauce', 'measurement' => '500gr', 'price' => 7.65, 'price_per_item' => 0.01530, 'supplier_name' => 'PFD'],
            ['category' => 'Dairy', 'item_name' => 'Cheese, Burrata', 'measurement' => '1kg', 'price' => 47.20, 'price_per_item' => 4.72000, 'supplier_name' => 'Two Providore'],
            ['category' => 'Dairy', 'item_name' => 'Cheese Cottage', 'measurement' => '500gr', 'price' => 6.30, 'price_per_item' => 0.01260, 'supplier_name' => 'Foodlink'],

            // --- DRY STORE ---
            ['category' => 'Dry Store', 'item_name' => 'Anchovy Headless Dry', 'measurement' => '500gr', 'price' => 10.42, 'price_per_item' => null, 'supplier_name' => 'HAC'],
            ['category' => 'Dry Store', 'item_name' => 'Balsamic Glazed', 'measurement' => '500mls', 'price' => 6.50, 'price_per_item' => null, 'supplier_name' => 'Foodlink'],
            ['category' => 'Dry Store', 'item_name' => 'Biscoff Spread Smooth', 'measurement' => '720gr', 'price' => 9.76, 'price_per_item' => null, 'supplier_name' => 'PFD'],
            ['category' => 'Dry Store', 'item_name' => 'Biscuit, Savoiardi lady Finger', 'measurement' => '400gr', 'price' => 5.10, 'price_per_item' => null, 'supplier_name' => 'Two Providores'],
            ['category' => 'Dry Store', 'item_name' => 'Bonito Shaving A-Grade, Dried', 'measurement' => '500gr', 'price' => 35.84, 'price_per_item' => null, 'supplier_name' => 'JFC'],

            // --- FROZEN ---
            ['category' => 'Frozen', 'item_name' => 'Chicken Nuggets Crumbed', 'measurement' => '6kg', 'price' => 61.50, 'price_per_item' => null, 'supplier_name' => 'Foodlink'],
            ['category' => 'Frozen', 'item_name' => 'Chips, Beer Battered', 'measurement' => '12kg', 'price' => 63.15, 'price_per_item' => null, 'supplier_name' => 'Two Providores'],
            ['category' => 'Frozen', 'item_name' => 'Corn Kernel', 'measurement' => '2kg', 'price' => 9.00, 'price_per_item' => 0.004500, 'supplier_name' => 'Foodlink'],
            ['category' => 'Frozen', 'item_name' => 'Croissant', 'measurement' => '60pcs', 'price' => 88.60, 'price_per_item' => 1.476667, 'supplier_name' => 'Eustralis'],

            // --- BREAD & PASTRY ---
            ['category' => 'Bread & Pastry', 'item_name' => 'Brioche Burger Bun 80gr', 'measurement' => '1ea', 'price' => 1.20, 'price_per_item' => 1.200000, 'supplier_name' => 'Luxe Bakery'],
            ['category' => 'Bread & Pastry', 'item_name' => 'Brioche Tin Loaf', 'measurement' => '1loaf', 'price' => 13.25, 'price_per_item' => null, 'supplier_name' => 'Bob & Pete'],
            ['category' => 'Bread & Pastry', 'item_name' => 'Café Country large', 'measurement' => '18 Slice', 'price' => 12.56, 'price_per_item' => 0.697778, 'supplier_name' => 'Sonoma'],
            ['category' => 'Bread & Pastry', 'item_name' => 'Milk Bun', 'measurement' => '6ea', 'price' => 8.21, 'price_per_item' => null, 'supplier_name' => 'Brasserie'],
            ['category' => 'Bread & Pastry', 'item_name' => 'Milk Hot Dog 100gr', 'measurement' => '1ea', 'price' => 0.95, 'price_per_item' => null, 'supplier_name' => 'Luxe Bakery'],

            // --- CHEMICAL & PACKAGING ---
            ['category' => 'Chemical & Packaging', 'item_name' => 'Blue Wipes Heavy Duty', 'measurement' => '90Sheets', 'price' => 7.40, 'price_per_item' => null, 'supplier_name' => 'B&E'],
            ['category' => 'Chemical & Packaging', 'item_name' => 'ClingWrap 45cm', 'measurement' => '600m', 'price' => 26.40, 'price_per_item' => null, 'supplier_name' => 'Foodlink'],
            ['category' => 'Chemical & Packaging', 'item_name' => 'Container Rect 500mls', 'measurement' => '500ea', 'price' => 44.00, 'price_per_item' => null, 'supplier_name' => 'Foodlink'],
            ['category' => 'Chemical & Packaging', 'item_name' => 'Container Rect 1000mls', 'measurement' => '500ea', 'price' => 79.75, 'price_per_item' => null, 'supplier_name' => 'Foodlink'],
        ];

        foreach ($items as $item) {
            SupplierItem::create($item);
        }
    }
}