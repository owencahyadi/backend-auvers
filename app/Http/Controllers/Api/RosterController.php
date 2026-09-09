<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Roster;
use App\Models\DailyRate;

class RosterController extends Controller
{
    public function calculatePayroll(Request $request)
    {
        $startDate = $request->query('start_date', '2026-07-13'); 
        $endDate = Carbon::parse($startDate)->endOfWeek()->format('Y-m-d');

        $employees = Employee::with(['dailyRate', 'rosters' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate])
                  ->orderBy('date', 'desc'); 
        }])->get();

        $result = [];

        foreach ($employees as $employee) {
            $cumulativeHours = 0;
            $rates = $employee->dailyRate;

            // CEK APAKAH PUNYA GAJI POKOK (BASE ALLOWANCE)
            $isFixedSalary = $rates ? (bool) $rates->is_fixed_salary : false;
            $fixedAmount = $rates ? floatval($rates->fixed_salary_amount) : 0;

            $thresholdHours = $rates ? floatval($rates->ot_threshold) : 38; 

            $displayRateWeekday = $rates ? $rates->rate_mon : 0;
            $displayRateSat = $rates ? $rates->rate_sat : 0;
            $displayRateSun = $rates ? $rates->rate_sun : 0;

            $payMon = 0; $payTue = 0; $payWed = 0; $payThu = 0; $payFri = 0;
            $paySat = 0; $paySun = 0;

            foreach ($employee->rosters as $shift) {
                
                $shift1_hours = 0;
                if ($shift->start_1 && $shift->end_1) {
                    $start1 = Carbon::parse($shift->start_1);
                    $end1 = Carbon::parse($shift->end_1);
                    $shift1_hours = $start1->diffInMinutes($end1) / 60;
                }

                $shift2_hours = 0;
                if ($shift->start_2 && $shift->end_2) {
                    $start2 = Carbon::parse($shift->start_2);
                    $end2 = Carbon::parse($shift->end_2);
                    $shift2_hours = $start2->diffInMinutes($end2) / 60;
                }

                $rate1 = floatval($shift->applied_rate ?? 0);
                $rate2 = $shift->applied_rate_2 !== null ? floatval($shift->applied_rate_2) : $rate1; 
                $overtimeRate = floatval($shift->applied_overtime_rate ?? 0);
                
                $dailyPay = 0;

                if ($shift1_hours > 0) {
                    if ($cumulativeHours >= $thresholdHours) {
                        $dailyPay += ($shift1_hours * $overtimeRate);
                    } elseif (($cumulativeHours + $shift1_hours) > $thresholdHours) {
                        $normalHours = $thresholdHours - $cumulativeHours;
                        $otHours = $shift1_hours - $normalHours;
                        $dailyPay += ($normalHours * $rate1) + ($otHours * $overtimeRate);
                    } else {
                        $dailyPay += ($shift1_hours * $rate1);
                    }
                    $cumulativeHours += $shift1_hours;
                }

                if ($shift2_hours > 0) {
                    if ($cumulativeHours >= $thresholdHours) {
                        $dailyPay += ($shift2_hours * $overtimeRate);
                    } elseif (($cumulativeHours + $shift2_hours) > $thresholdHours) {
                        $normalHours = $thresholdHours - $cumulativeHours;
                        $otHours = $shift2_hours - $normalHours;
                        $dailyPay += ($normalHours * $rate2) + ($otHours * $overtimeRate);
                    } else {
                        $dailyPay += ($shift2_hours * $rate2);
                    }
                    $cumulativeHours += $shift2_hours;
                }

                if ($shift1_hours == 0 && $shift2_hours == 0 && $shift->total_hours > 0) {
                    $hours = floatval($shift->total_hours);
                    if ($cumulativeHours >= $thresholdHours) {
                        $dailyPay += ($hours * $overtimeRate);
                    } elseif (($cumulativeHours + $hours) > $thresholdHours) {
                        $normalHours = $thresholdHours - $cumulativeHours;
                        $otHours = $hours - $normalHours;
                        $dailyPay += ($normalHours * $rate1) + ($otHours * $overtimeRate);
                    } else {
                        $dailyPay += ($hours * $rate1);
                    }
                    $cumulativeHours += $hours;
                }

                $dayName = strtolower(Carbon::parse($shift->date)->format('l'));
                
                if (in_array($dayName, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
                    $displayRateWeekday = $rate1;
                } elseif ($dayName == 'saturday') {
                    $displayRateSat = $rate1;
                } elseif ($dayName == 'sunday') {
                    $displayRateSun = $rate1;
                }

                switch($dayName) {
                    case 'monday': $payMon += $dailyPay; break;
                    case 'tuesday': $payTue += $dailyPay; break;
                    case 'wednesday': $payWed += $dailyPay; break;
                    case 'thursday': $payThu += $dailyPay; break;
                    case 'friday': $payFri += $dailyPay; break;
                    case 'saturday': $paySat += $dailyPay; break;
                    case 'sunday': $paySun += $dailyPay; break;
                }
            }

            // Hitung total murni dari jam kerja
            $totalWeekday = $payMon + $payTue + $payWed + $payThu + $payFri;
            $grandTotal = $totalWeekday + $paySat + $paySun;

            // LOGIKA BARU: Tambahkan Base Salary ke Grand Total tanpa menghapus jam-jaman
            if ($isFixedSalary && $fixedAmount > 0) {
                $grandTotal += $fixedAmount;
            }

            $result[] = [
                'id' => $employee->id,
                'name' => $employee->name,
                'is_fixed_salary' => $isFixedSalary,     
                'fixed_salary_amount' => $fixedAmount,   
                'rate_weekday' => $displayRateWeekday,
                'rate_sat' => $displayRateSat,
                'rate_sun' => $displayRateSun,
                'ot_threshold' => $thresholdHours,
                'pay_mon' => $payMon,
                'pay_tue' => $payTue,
                'pay_wed' => $payWed,
                'pay_thu' => $payThu,
                'pay_fri' => $payFri,
                'total_weekday' => $totalWeekday,
                'pay_sat' => $paySat,
                'pay_sun' => $paySun,
                'grand_total' => $grandTotal
            ];
        }

        $weeklySale = \App\Models\WeeklySale::where('week_start_date', $startDate)->first();
        
        $totalSales = 0;
        if ($weeklySale) {
            $totalSales = floatval($weeklySale->food) + 
                          floatval($weeklySale->beverage) + 
                          floatval($weeklySale->alcohol) + 
                          floatval($weeklySale->stall);
        }

        return response()->json([
            'status' => 'success', 
            'data' => $result,
            'total_sales' => $totalSales 
        ]);
    }

    public function getEmployees()
    {
        $employees = Employee::select('id', 'name', 'position')->orderBy('id', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $employees
        ]);
    }

    public function storeShift(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
        ]);

        $totalHours = 0;
        
        if (!$request->is_unavailable) {
            if ($request->start_1 && $request->end_1) {
                $start = \Carbon\Carbon::parse($request->start_1);
                $end = \Carbon\Carbon::parse($request->end_1);
                $totalHours += $start->diffInMinutes($end) / 60;
            }
            if ($request->start_2 && $request->end_2) {
                $start2 = \Carbon\Carbon::parse($request->start_2);
                $end2 = \Carbon\Carbon::parse($request->end_2);
                $totalHours += $start2->diffInMinutes($end2) / 60;
            }
        }

        $existingShift = Roster::where('employee_id', $request->employee_id)
                               ->where('date', $request->date)
                               ->first();

        $dataToSave = [
            'is_unavailable' => $request->is_unavailable ?? false,
            'start_1' => $request->start_1,
            'end_1' => $request->end_1,
            'role_1' => $request->role_1,
            'start_2' => $request->start_2,
            'end_2' => $request->end_2,
            'role_2' => $request->role_2,
            'total_hours' => $totalHours,
            'applied_rate_2' => $request->applied_rate_2 !== null && $request->applied_rate_2 !== '' ? $request->applied_rate_2 : null,
        ];

        if ($existingShift) {
            $existingShift->update($dataToSave);
        } else {
            $rates = \App\Models\DailyRate::where('employee_id', $request->employee_id)->first();
            $dayName = strtolower(\Carbon\Carbon::parse($request->date)->format('D'));
            $rateColumn = 'rate_' . $dayName;
            
            $dataToSave['employee_id'] = $request->employee_id;
            $dataToSave['date'] = $request->date;
            $dataToSave['applied_rate'] = $rates ? $rates->$rateColumn : 0;
            $dataToSave['applied_overtime_rate'] = $rates ? $rates->overtime_rate : 0;

            Roster::create($dataToSave);
        }

        return response()->json(['status' => 'success', 'message' => 'Shift berhasil disimpan!']);
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'position' => 'required|string',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
            'position' => $request->position,
            'level' => 'Standard' 
        ]);

        $dailyRate = new DailyRate();
        $dailyRate->employee_id = $employee->id;
        $dailyRate->is_fixed_salary = $request->is_fixed_salary ? 1 : 0;
        $dailyRate->fixed_salary_amount = is_numeric($request->fixed_salary_amount) ? (float)$request->fixed_salary_amount : 0;
        
        $dailyRate->rate_mon = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
        $dailyRate->rate_tue = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
        $dailyRate->rate_wed = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
        $dailyRate->rate_thu = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
        $dailyRate->rate_fri = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
        
        $dailyRate->rate_sat = is_numeric($request->rate_sat) ? (float)$request->rate_sat : 0;
        $dailyRate->rate_sun = is_numeric($request->rate_sun) ? (float)$request->rate_sun : 0;
        $dailyRate->overtime_rate = is_numeric($request->overtime_rate) ? (float)$request->overtime_rate : 0;
        $dailyRate->ot_threshold = is_numeric($request->ot_threshold) ? (float)$request->ot_threshold : 38;
        
        $dailyRate->save(); 

        return response()->json([
            'status' => 'success',
            'message' => 'Karyawan dan Rate berhasil ditambahkan!'
        ]);
    }

    public function updateRate(Request $request, $id)
    {
        $dailyRate = DailyRate::where('employee_id', $id)->first();
        
        if ($dailyRate) {
            $dailyRate->is_fixed_salary = $request->is_fixed_salary ? 1 : 0;
            $dailyRate->fixed_salary_amount = is_numeric($request->fixed_salary_amount) ? (float)$request->fixed_salary_amount : 0;
            
            $dailyRate->rate_mon = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
            $dailyRate->rate_tue = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
            $dailyRate->rate_wed = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
            $dailyRate->rate_thu = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
            $dailyRate->rate_fri = is_numeric($request->base_rate) ? (float)$request->base_rate : 0;
            
            $dailyRate->rate_sat = is_numeric($request->rate_sat) ? (float)$request->rate_sat : 0;
            $dailyRate->rate_sun = is_numeric($request->rate_sun) ? (float)$request->rate_sun : 0;
            $dailyRate->overtime_rate = is_numeric($request->overtime_rate) ? (float)$request->overtime_rate : 0;
            $dailyRate->ot_threshold = is_numeric($request->ot_threshold) ? (float)$request->ot_threshold : 38;
            
            $dailyRate->save(); 

            return response()->json(['status' => 'success', 'message' => 'Rate berhasil diubah!']);
        }

        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
    }

    public function updateWeeklyRate(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'base_rate' => 'required|numeric',
            'rate_sat' => 'required|numeric', 
            'rate_sun' => 'required|numeric', 
            'overtime_rate' => 'required|numeric',
            'ot_threshold' => 'sometimes|numeric' 
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dayName = strtolower($currentDate->format('D')); 
            
            if ($dayName === 'sat') {
                $rateValue = $request->rate_sat;
            } elseif ($dayName === 'sun') {
                $rateValue = $request->rate_sun;
            } else {
                $rateValue = $request->base_rate;
            }

            $existingShift = Roster::where('employee_id', $id)
                                   ->where('date', $currentDate->format('Y-m-d'))
                                   ->first();

            if ($existingShift) {
                $existingShift->update([
                    'applied_rate' => $rateValue,
                    'applied_overtime_rate' => $request->overtime_rate
                ]);
            } else {
                Roster::create([
                    'employee_id' => $id,
                    'date' => $currentDate->format('Y-m-d'),
                    'total_hours' => 0,
                    'applied_rate' => $rateValue,
                    'applied_overtime_rate' => $request->overtime_rate
                ]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Rate khusus minggu ini berhasil diterapkan untuk semua hari!']);
    }

    public function getCalendarData(Request $request)
    {
        $startDate = $request->query('start_date', '2026-07-13');
        $endDate = \Carbon\Carbon::parse($startDate)->endOfWeek()->format('Y-m-d');

        $employeeIds = Employee::pluck('id');

        $rosters = Roster::whereIn('employee_id', $employeeIds)
                         ->whereBetween('date', [$startDate, $endDate])
                         ->get();
        
        return response()->json([
            'status' => 'success', 
            'data' => $rosters
        ]);
    }

    public function updateEmployee(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'position' => 'required|string',
        ]);

        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Karyawan tidak ditemukan'], 404);
        }

        $employee->update([
            'name' => $request->name,
            'position' => $request->position,
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Data karyawan berhasil diubah!'
        ]);
    }

    public function destroyEmployee($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Karyawan tidak ditemukan'], 404);
        }

        $employee->delete();

        return response()->json([
            'status' => 'success', 
            'message' => 'Karyawan berhasil dihapus!'
        ]);
    }
}