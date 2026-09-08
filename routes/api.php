<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RosterController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\UserController;

// ---------------------------------------------------------
// Rute Publik (Tidak perlu token untuk akses)
// ---------------------------------------------------------
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Rute ini bisa diakses siapa saja (Apakah seharusnya dilindungi token juga?)
Route::get('/calculate-payroll', [RosterController::class, 'calculatePayroll']);
Route::get('/employees', [RosterController::class, 'getEmployees']); 
Route::post('/rosters', [RosterController::class, 'storeShift']); 
Route::post('/employees', [RosterController::class, 'storeEmployee']);
Route::put('/employees/{id}/rates', [RosterController::class, 'updateRate']);
Route::put('/employees/{id}/weekly-rates', [App\Http\Controllers\Api\RosterController::class, 'updateWeeklyRate']);
Route::put('/employees/{id}', [RosterController::class, 'updateEmployee']);
Route::delete('/employees/{id}', [RosterController::class, 'destroyEmployee']);
Route::get('/rosters/calendar', [App\Http\Controllers\Api\RosterController::class, 'getCalendarData']);
Route::apiResource('supplier-items', App\Http\Controllers\SupplierItemController::class);
Route::apiResource('purchases', App\Http\Controllers\PurchaseController::class)->except(['update', 'show']);
Route::get('/profit-loss', [ProfitLossController::class, 'index']);
Route::apiResource('operational-costs', App\Http\Controllers\OperationalCostController::class)->except(['update', 'show']);
Route::get('/profit-loss/monthly', [App\Http\Controllers\ProfitLossController::class, 'monthly']);
Route::post('/profit-loss/sales', [ProfitLossController::class, 'saveSales']);
Route::get('/stores', [StoreController::class, 'index']);
Route::post('/stores', [StoreController::class, 'store']);
Route::put('/stores/{id}', [StoreController::class, 'update']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);


// ---------------------------------------------------------
// Rute Privat (Wajib pakai Token / Harus Login dulu)
// ---------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    // Rute Logout wajib pakai token agar sistem tahu siapa yang mau logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Ambil data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});