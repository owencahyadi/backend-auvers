<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OperationalCost;
use Illuminate\Support\Facades\DB;

class OperationalCostSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan tabel terlebih dahulu
        DB::statement('TRUNCATE TABLE operational_costs RESTART IDENTITY CASCADE;');

        $costs = [
            [
                'date' => '2026-06-23',
                'name' => 'Listrik Toko (PLN)',
                'amount' => 150.00,
            ],
            [
                'date' => '2026-06-24',
                'name' => 'Gas LPG 50kg',
                'amount' => 85.50,
            ],
            [
                'date' => '2026-06-25',
                'name' => 'Internet & WiFi Bulanan',
                'amount' => 45.00,
            ],
            [
                'date' => '2026-06-26',
                'name' => 'Air Bersih (PAM)',
                'amount' => 60.00,
            ],
        ];

        foreach ($costs as $c) {
            OperationalCost::create($c);
        }
    }
}