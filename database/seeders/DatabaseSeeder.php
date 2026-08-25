<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\DailyRate;
use App\Models\Roster;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Data Karyawan
        $jacopo = Employee::create([
            'name' => 'Jacopo',
            'position' => 'Sous Chef',
            'level' => 'Lvl 6'
        ]);

        // 2. Buat Harga (Rate) per Hari untuk Jacopo
        DailyRate::create([
            'employee_id' => $jacopo->id,
            'rate_mon' => 27.02,
            'rate_tue' => 27.02,
            'rate_wed' => 27.02,
            'rate_thu' => 27.02,
            'rate_fri' => 27.02,
            'rate_sat' => 33.77,
            'rate_sun' => 40.53,
            'overtime_rate' => 40.53 
        ]);

        // 3. Masukkan Jadwal Roster (Simulasi Total Jam Kerja)
        // Tanggal diset dari Minggu (28 Juni 2026) mundur ke Senin
        $shifts = [
            ['date' => '2026-06-28', 'hours' => 9.00], // Minggu
            ['date' => '2026-06-27', 'hours' => 11.25], // Sabtu
            ['date' => '2026-06-26', 'hours' => 11.00], // Jumat
            ['date' => '2026-06-25', 'hours' => 11.00], // Kamis
            ['date' => '2026-06-24', 'hours' => 11.75], // Rabu
            ['date' => '2026-06-23', 'hours' => 7.50],  // Selasa
            ['date' => '2026-06-22', 'hours' => 7.50],  // Senin
        ];

        foreach ($shifts as $shift) {
            Roster::create([
                'employee_id' => $jacopo->id,
                'date' => $shift['date'],
                'total_hours' => $shift['hours']
            ]);
        }
    }
}