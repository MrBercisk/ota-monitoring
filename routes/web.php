<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DelayController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StationController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Stations
    Route::get('stations', [StationController::class, 'index'])->name('stations.index');
    Route::get('stations/create', [StationController::class, 'create'])->name('stations.create');
    Route::post('stations', [StationController::class, 'store'])->name('stations.store');
    Route::get('stations/{station}/edit', [StationController::class, 'edit'])->name('stations.edit');
    Route::put('stations/{station}', [StationController::class, 'update'])->name('stations.update');
    Route::delete('stations/{station}', [StationController::class, 'destroy'])->name('stations.destroy');

    // Flights
    Route::get('flights', [FlightController::class, 'index'])->name('flights.index');
    Route::get('flights/create', [FlightController::class, 'create'])->name('flights.create');
    Route::post('flights', [FlightController::class, 'store'])->name('flights.store');
    Route::get('flights/{flight}/edit', [FlightController::class, 'edit'])->name('flights.edit');
    Route::put('flights/{flight}', [FlightController::class, 'update'])->name('flights.update');
    Route::delete('flights/{flight}', [FlightController::class, 'destroy'])->name('flights.destroy');

    Route::get('flights/export/weekly', [FlightController::class, 'exportWeekly'])
        ->name('flights.export.weekly');

    Route::get('flights/export/monthly', [FlightController::class, 'exportMonthly'])
        ->name('flights.export.monthly');

    // Delay
    Route::get('delay', [DelayController::class, 'index'])->name('delay.index');
    Route::get('delay/create', [DelayController::class, 'create'])->name('delay.create');
    Route::post('delay', [DelayController::class, 'store'])->name('delay.store');
    Route::get('delay/{flight}/edit', [DelayController::class, 'edit'])->name('delay.edit');
    Route::put('delay/{flight}', [DelayController::class, 'update'])->name('delay.update');
    Route::delete('delay/{flight}', [DelayController::class, 'destroy'])->name('delay.destroy');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';