<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\DailyRate;
use App\Models\Roster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExcelSeeder extends Seeder
{
    public function run()
    {
        // 1. Bersihkan Semua Data Lama (Khusus PostgreSQL / Supabase)
        DB::statement('TRUNCATE TABLE rosters, daily_rates, employees RESTART IDENTITY CASCADE;');

        // 2. Buat Daftar Karyawan Sesuai Excel
        $names = ['Kim', 'Jacopo', 'David', 'Aliung', 'Eka', 'Bryan', 'Jeff', 'Aphin', 'Helly', 'Leon', 'Abdullah', 'Baghiya', 'Ryo', 'William'];
        $empMap = [];
        
        foreach($names as $name) {
            $emp = Employee::create(['name' => $name, 'position' => 'Kitchen Staff']);
            // Buat rate palsu agar halaman payroll tidak error
            DailyRate::create([
                'employee_id' => $emp->id,
                'rate_mon' => 25, 'rate_tue' => 25, 'rate_wed' => 25, 'rate_thu' => 25, 'rate_fri' => 25,
                'rate_sat' => 28, 'rate_sun' => 30, 'overtime_rate' => 35
            ]);
            $empMap[$name] = $emp->id;
        }

        // 3. Masukkan Jadwal (Senin, 22 Jun 2026 - Minggu, 28 Jun 2026)
        $schedules = [
            // KIM
            ['name' => 'Kim', 'date' => '2026-06-22', 's1' => '09:30', 'e1' => '15:00', 'r1' => 'Noodle'],
            ['name' => 'Kim', 'date' => '2026-06-23', 's1' => '08:00', 'e1' => '15:00', 'r1' => 'Pan'],
            ['name' => 'Kim', 'date' => '2026-06-24', 'unavail' => true],
            ['name' => 'Kim', 'date' => '2026-06-26', 's1' => '08:00', 'e1' => '15:30', 'r1' => 'Pan'],

            // JACOPO
            ['name' => 'Jacopo', 'date' => '2026-06-22', 's1' => '08:00', 'e1' => '15:30', 'r1' => 'Larder'],
            ['name' => 'Jacopo', 'date' => '2026-06-23', 's1' => '07:30', 'e1' => '14:15', 'r1' => 'Larder', 's2' => '16:30', 'e2' => '21:30', 'r2' => 'Pan'],
            ['name' => 'Jacopo', 'date' => '2026-06-24', 's1' => '08:00', 'e1' => '14:30', 'r1' => 'Pan', 's2' => '16:30', 'e2' => '21:00', 'r2' => 'Pass'],
            ['name' => 'Jacopo', 'date' => '2026-06-25', 's1' => '08:00', 'e1' => '14:00', 'r1' => 'Pan', 's2' => '16:00', 'e2' => '21:00', 'r2' => 'Pass'],
            ['name' => 'Jacopo', 'date' => '2026-06-27', 's1' => '08:30', 'e1' => '14:30', 'r1' => 'Pass', 's2' => '16:00', 'e2' => '21:15', 'r2' => 'Pass'],
            ['name' => 'Jacopo', 'date' => '2026-06-28', 's1' => '08:30', 'e1' => '15:00', 'r1' => 'Pass', 's2' => '16:00', 'e2' => '18:30', 'r2' => 'Pass'],

            // DAVID
            ['name' => 'David', 'date' => '2026-06-22', 's1' => '07:30', 'e1' => '15:30', 'r1' => 'Pan'],
            ['name' => 'David', 'date' => '2026-06-23', 'unavail' => true],
            ['name' => 'David', 'date' => '2026-06-24', 's1' => '07:30', 'e1' => '14:30', 'r1' => 'Larder', 's2' => '16:30', 'e2' => '21:00', 'r2' => 'Pan'],
            ['name' => 'David', 'date' => '2026-06-25', 'unavail' => true],
            ['name' => 'David', 'date' => '2026-06-26', 's1' => '07:30', 'e1' => '14:00', 'r1' => 'Larder', 's2' => '16:00', 'e2' => '21:00', 'r2' => 'Pass'],
            ['name' => 'David', 'date' => '2026-06-27', 's1' => '08:30', 'e1' => '12:45', 'r1' => 'Pan'],
            ['name' => 'David', 'date' => '2026-06-28', 's1' => '08:30', 'e1' => '16:00', 'r1' => 'Pan'],

            // ALIUNG
            ['name' => 'Aliung', 'date' => '2026-06-23', 'unavail' => true],
            ['name' => 'Aliung', 'date' => '2026-06-25', 'unavail' => true],
            ['name' => 'Aliung', 'date' => '2026-06-26', 's1' => '17:00', 'e1' => '20:30', 'r1' => 'Noodle'],

            // BRYAN
            ['name' => 'Bryan', 'date' => '2026-06-22', 's1' => '10:00', 'e1' => '16:00', 'r1' => 'Larder'],
            ['name' => 'Bryan', 'date' => '2026-06-23', 's1' => '11:00', 'e1' => '15:00', 'r1' => 'Kitchen Hand', 's2' => '17:00', 'e2' => '21:30', 'r2' => 'Kitchen Hand'],
            ['name' => 'Bryan', 'date' => '2026-06-25', 's1' => '07:30', 'e1' => '14:00', 'r1' => 'Larder', 's2' => '16:30', 'e2' => '21:00', 'r2' => 'Pan'],
            ['name' => 'Bryan', 'date' => '2026-06-26', 's1' => '11:00', 'e1' => '14:15', 'r1' => 'Kitchen Hand', 's2' => '16:30', 'e2' => '21:00', 'r2' => 'Pan'],
            ['name' => 'Bryan', 'date' => '2026-06-27', 's1' => '10:00', 'e1' => '14:30', 'r1' => 'Larder', 's2' => '16:30', 'e2' => '21:15', 'r2' => 'Pan'],
            ['name' => 'Bryan', 'date' => '2026-06-28', 's1' => '09:00', 'e1' => '14:00', 'r1' => 'Larder', 's2' => '15:00', 'e2' => '20:30', 'r2' => 'Pan'],

            // HELLY
            ['name' => 'Helly', 'date' => '2026-06-23', 's1' => '17:30', 'e1' => '21:30', 'r1' => 'Larder'],
            ['name' => 'Helly', 'date' => '2026-06-24', 's1' => '17:00', 'e1' => '21:00', 'r1' => 'Larder'],
            ['name' => 'Helly', 'date' => '2026-06-25', 's1' => '17:00', 'e1' => '18:45', 'r1' => 'Larder'],
            ['name' => 'Helly', 'date' => '2026-06-27', 's1' => '17:00', 'e1' => '21:15', 'r1' => 'Larder'],
            ['name' => 'Helly', 'date' => '2026-06-28', 's1' => '17:00', 'e1' => '20:30', 'r1' => 'Larder'],

            // LEON
            ['name' => 'Leon', 'date' => '2026-06-24', 's1' => '11:00', 'e1' => '14:45', 'r1' => 'Kitchen Hand', 's2' => '17:00', 'e2' => '21:00', 'r2' => 'Kitchen Hand'],
            ['name' => 'Leon', 'date' => '2026-06-25', 's1' => '11:00', 'e1' => '14:15', 'r1' => 'Kitchen Hand', 's2' => '17:00', 'e2' => '21:00', 'r2' => 'Kitchen Hand'],
            ['name' => 'Leon', 'date' => '2026-06-27', 's1' => '11:00', 'e1' => '15:00', 'r1' => 'Kitchen Hand', 's2' => '17:00', 'e2' => '21:30', 'r2' => 'Kitchen Hand'],

            // ABDULLAH
            ['name' => 'Abdullah', 'date' => '2026-06-26', 's1' => '17:00', 'e1' => '21:30', 'r1' => 'Kitchen Hand'],
            ['name' => 'Abdullah', 'date' => '2026-06-28', 's1' => '11:00', 'e1' => '15:30', 'r1' => 'Kitchen Hand', 's2' => '17:00', 'e2' => '21:00', 'r2' => 'Kitchen Hand'],
        ];

        foreach ($schedules as $s) {
            $total = 0;
            if (empty($s['unavail'])) {
                if (isset($s['s1']) && isset($s['e1'])) {
                    $total += Carbon::parse($s['s1'])->diffInMinutes(Carbon::parse($s['e1'])) / 60;
                }
                if (isset($s['s2']) && isset($s['e2'])) {
                    $total += Carbon::parse($s['s2'])->diffInMinutes(Carbon::parse($s['e2'])) / 60;
                }
            }

            Roster::create([
                'employee_id' => $empMap[$s['name']],
                'date' => $s['date'],
                'is_unavailable' => $s['unavail'] ?? false,
                'start_1' => $s['s1'] ?? null,
                'end_1' => $s['e1'] ?? null,
                'role_1' => $s['r1'] ?? null,
                'start_2' => $s['s2'] ?? null,
                'end_2' => $s['e2'] ?? null,
                'role_2' => $s['r2'] ?? null,
                'total_hours' => $total,
                'applied_rate' => 25,
                'applied_overtime_rate' => 35
            ]);
        }
    }
}