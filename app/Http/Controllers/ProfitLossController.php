<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Employee;
use App\Models\Roster;
use App\Models\OperationalCost;
use App\Models\WeeklySale;
use Carbon\Carbon;

class ProfitLossController extends Controller
{
    /**
     * Endpoint untuk Profit & Loss (Bisa menerima parameter start_date atau month)
     */
    public function index(Request $request)
    {
        // Mendukung filter berdasarkan start_date atau month dari frontend
        $startDate = $request->input('start_date');
        $monthInput = $request->input('month');

        if ($startDate) {
            $start = Carbon::parse($startDate);
        } elseif ($monthInput) {
            $start = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } else {
            $start = Carbon::now()->startOfWeek();
        }

        $end = (clone $start)->addDays(6);

        // 1. LABOR COST (Otomatis terfilter per toko karena Employee menggunakan BelongsToStore Trait)
        $employees = Employee::with(['rosters' => function($query) use ($start, $end) {
            $query->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                  ->orderBy('date', 'asc');
        }])->get();

        $totalLaborCost = 0;
        $thresholdHours = 38; 

        foreach ($employees as $emp) {
            $cumulativeHours = 0;
            
            foreach ($emp->rosters as $ros) {
                if (!$ros->is_unavailable) {
                    $hours = $ros->total_hours ?? 0;
                    
                    if (!is_null($ros->applied_rate)) {
                        $dailyRate = floatval($ros->applied_rate);
                        $overtimeRate = floatval($ros->applied_overtime_rate);
                    } else {
                        $dayOfWeek = Carbon::parse($ros->date)->dayOfWeek;
                        if ($dayOfWeek == 0) {
                            $dailyRate = $emp->rate_sun ?? 25;
                        } elseif ($dayOfWeek == 6) {
                            $dailyRate = $emp->rate_sat ?? 25;
                        } else {
                            $dailyRate = $emp->base_rate ?? 20;
                        }
                        $overtimeRate = $emp->overtime_rate ?? 30; 
                    }
                    
                    $dailyPay = 0;
                    if ($cumulativeHours >= $thresholdHours) {
                        $dailyPay = ($hours * $overtimeRate);
                    } elseif (($cumulativeHours + $hours) > $thresholdHours) {
                        $normalHours = $thresholdHours - $cumulativeHours;
                        $otHours = $hours - $normalHours;
                        $dailyPay = ($normalHours * $dailyRate) + ($otHours * $overtimeRate);
                    } else {
                        $dailyPay = ($hours * $dailyRate);
                    }
                    
                    $cumulativeHours += $hours;
                    $totalLaborCost += $dailyPay;
                }
            }
        }

        // 2. COGS (PURCHASES) - Otomatis terfilter per toko berkat Trait
        $purchases = Purchase::with('supplierItem')
            ->whereBetween('purchase_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();

        $cogsByCategory = [
            'Food' => 0,
            'Beverage' => 0,
            'Alcohol' => 0,
            'Stall' => 0,
            'Packaging' => 0 
        ];
        $totalCogs = 0;
        
        foreach ($purchases as $p) {
            $rawCategory = $p->supplierItem->category ?? 'Lainnya';
            $price = floatval($p->total_price);

            if (in_array($rawCategory, ['Meat', 'Seafood', 'Vege', 'Dairy', 'Dry Store', 'Frozen', 'Bread & Pastry'])) {
                $cogsByCategory['Food'] += $price;
            } elseif ($rawCategory === 'Alcohol') {
                $cogsByCategory['Alcohol'] += $price;
            } elseif ($rawCategory === 'Stall') {
                $cogsByCategory['Stall'] += $price;
            } elseif (in_array($rawCategory, ['Chemical & Packaging'])) {
                $cogsByCategory['Packaging'] += $price; 
            } else {
                $cogsByCategory['Beverage'] += $price;
            }

            $totalCogs += $price;
        }

        // 3. OPERATIONAL COSTS (OpEx) - Otomatis terfilter per toko berkat Trait
        $operationalCosts = OperationalCost::whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])->get();
        $totalOpEx = $operationalCosts->sum('amount');

        // 4. SALES / REVENUE - Ambil dari WeeklySale (Otomatis terfilter per toko berkat Trait)
        $weeklySaleData = WeeklySale::where('week_start_date', $start->format('Y-m-d'))->first();
        
        $sales = [
            'Food' => $weeklySaleData ? floatval($weeklySaleData->food) : 0,
            'Beverage' => $weeklySaleData ? floatval($weeklySaleData->beverage) : 0,
            'Alcohol' => $weeklySaleData ? floatval($weeklySaleData->alcohol) : 0,
            'Stall' => $weeklySaleData ? floatval($weeklySaleData->stall) : 0,
        ];
        $totalSales = array_sum($sales);

        // 5. KALKULASI LABA
        $grossProfit = $totalSales - $totalCogs;
        $netProfit = $grossProfit - $totalLaborCost - $totalOpEx;

        // Menyusun format response agar sinkron dengan Frontend P&L
        $breakdown = [
            ['category_name' => 'Food Sales', 'type' => 'Income', 'amount' => $sales['Food']],
            ['category_name' => 'Beverage & Alcohol Sales', 'type' => 'Income', 'amount' => $sales['Beverage'] + $sales['Alcohol']],
            ['category_name' => 'Food & Beverage COGS', 'type' => 'Expense', 'amount' => $cogsByCategory['Food'] + $cogsByCategory['Beverage']],
            ['category_name' => 'Labor Cost (Payroll)', 'type' => 'Expense', 'amount' => $totalLaborCost],
            ['category_name' => 'Operational Expenses', 'type' => 'Expense', 'amount' => $totalOpEx],
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'week_range' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
                'sales' => $sales,
                'total_revenue' => $totalSales,
                'total_cogs' => $totalCogs,
                'cogs_by_category' => $cogsByCategory,
                'gross_profit' => $grossProfit,
                'total_labor_cost' => $totalLaborCost,
                'operational_costs' => $operationalCosts,
                'total_opex' => $totalOpEx,
                'net_profit' => $netProfit,
                'breakdown' => $breakdown
            ]
        ]);
    }

    public function saveSales(Request $request)
    {
        $request->validate([
            'weeks' => 'required|array'
        ]);

        foreach ($request->weeks as $week) {
            WeeklySale::updateOrCreate(
                ['week_start_date' => $week['start']], 
                [
                    'food' => $week['sales']['Food'] ?? 0,
                    'beverage' => $week['sales']['Beverage'] ?? 0,
                    'alcohol' => $week['sales']['Alcohol'] ?? 0,
                    'stall' => $week['sales']['Stall'] ?? 0,
                ]
            );
        }

        return response()->json(['message' => 'Data Sales berhasil disimpan!']);
    }

    public function monthly(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $year = date('Y', strtotime($month));
        $monthNum = date('m', strtotime($month));
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

        $weeks = [];
        $startDay = 1;
        
        while ($startDay <= $daysInMonth) {
            $endDay = min($startDay + 6, $daysInMonth);
            $startDate = sprintf('%04d-%02d-%02d', $year, $monthNum, $startDay);
            $endDate = sprintf('%04d-%02d-%02d', $year, $monthNum, $endDay);
            $label = date('j M', strtotime($startDate)) . ' - ' . date('j M', strtotime($endDate));
            
            $weeks[] = [
                'label' => $label,
                'start' => $startDate,
                'end' => $endDate
            ];
            $startDay += 7;
        }

        $report = [];
        $thresholdHours = 38;

        foreach ($weeks as $week) {
            $start = $week['start'];
            $end = $week['end'];

            // Otomatis terfilter per toko berkat BelongsToStore Trait
            $employees = Employee::with(['rosters' => function($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end])->orderBy('date', 'asc');
            }])->get();

            $weeklyLabor = 0;
            foreach ($employees as $emp) {
                $cumulativeHours = 0;

                foreach ($emp->rosters as $ros) {
                    if (!$ros->is_unavailable) {
                        $hours = $ros->total_hours ?? 0;
                        if (!is_null($ros->applied_rate)) {
                            $dailyRate = floatval($ros->applied_rate);
                            $overtimeRate = floatval($ros->applied_overtime_rate);
                        } else {
                            $dayOfWeek = Carbon::parse($ros->date)->dayOfWeek;
                            $dailyRate = ($dayOfWeek == 0) ? ($emp->rate_sun ?? 25) : (($dayOfWeek == 6) ? ($emp->rate_sat ?? 25) : ($emp->base_rate ?? 20));
                            $overtimeRate = $emp->overtime_rate ?? 30;
                        }
                        
                        $dailyPay = 0;
                        if ($cumulativeHours >= $thresholdHours) {
                            $dailyPay = ($hours * $overtimeRate);
                        } elseif (($cumulativeHours + $hours) > $thresholdHours) {
                            $normalHours = $thresholdHours - $cumulativeHours;
                            $otHours = $hours - $normalHours;
                            $dailyPay = ($normalHours * $dailyRate) + ($otHours * $overtimeRate);
                        } else {
                            $dailyPay = ($hours * $dailyRate);
                        }
                        $cumulativeHours += $hours;
                        $weeklyLabor += $dailyPay;
                    }
                }
            }

            $purchases = Purchase::with('supplierItem')
                ->whereBetween('purchase_date', [$start, $end])
                ->get();
                
            $cogsByCat = [
                'Food' => 0, 'Beverage' => 0, 'Alcohol' => 0, 'Stall' => 0, 'Packaging' => 0
            ];
            $weeklyCogs = 0;
            
            foreach ($purchases as $p) {
                $rawCategory = $p->supplierItem->category ?? 'Lainnya';
                $price = floatval($p->total_price);
    
                if (in_array($rawCategory, ['Meat', 'Seafood', 'Vege', 'Dairy', 'Dry Store', 'Frozen', 'Bread & Pastry'])) {
                    $cogsByCat['Food'] += $price;
                } elseif ($rawCategory === 'Alcohol') {
                    $cogsByCat['Alcohol'] += $price;
                } elseif ($rawCategory === 'Stall') {
                    $cogsByCat['Stall'] += $price;
                } elseif (in_array($rawCategory, ['Chemical & Packaging'])) {
                    $cogsByCat['Packaging'] += $price; 
                } else {
                    $cogsByCat['Beverage'] += $price;
                }
                $weeklyCogs += $price;
            }

            $opex = OperationalCost::whereBetween('date', [$start, $end])->sum('amount');
            $weeklySaleData = WeeklySale::where('week_start_date', $start)->first();
            
            $sales = [
                'Food' => $weeklySaleData ? floatval($weeklySaleData->food) : 0,
                'Beverage' => $weeklySaleData ? floatval($weeklySaleData->beverage) : 0,
                'Alcohol' => $weeklySaleData ? floatval($weeklySaleData->alcohol) : 0,
                'Stall' => $weeklySaleData ? floatval($weeklySaleData->stall) : 0,
            ];
            $totalSales = array_sum($sales);

            $grossProfit = $totalSales - $weeklyCogs;
            $netProfit = $grossProfit - $weeklyLabor - $opex;

            $report[] = [
                'week_label' => $week['label'],
                'start' => $start, 
                'end' => $end,
                'sales' => $sales,
                'total_sales' => $totalSales,
                'cogs_by_category' => $cogsByCat,
                'total_cogs' => $weeklyCogs,
                'gross_profit' => $grossProfit,
                'labor_cost' => $weeklyLabor,
                'operational_costs' => $opex,
                'net_profit' => $netProfit,
            ];
        }

        return response()->json([
            'status' => 'success',
            'month_name' => date('F Y', strtotime($month)),
            'weeks' => $report
        ]);
    }
}