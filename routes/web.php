<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DelayCategoryController;
use App\Http\Controllers\DelayController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\FlightScheduleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Stations
    Route::get('stations', [StationController::class, 'index'])->name('stations.index');
    Route::get('stations/datatable', [StationController::class, 'datatable'])->name('stations.datatable');
    Route::get('stations/create', [StationController::class, 'create'])->name('stations.create');
    Route::post('stations', [StationController::class, 'store'])->name('stations.store');
    Route::get('stations/{station}/edit', [StationController::class, 'edit'])->name('stations.edit');
    Route::put('stations/{station}', [StationController::class, 'update'])->name('stations.update');
    Route::delete('stations/{station}', [StationController::class, 'destroy'])->name('stations.destroy');

    // Flights
    Route::get('flights', [FlightController::class, 'index'])->name('flights.index');
    Route::get('flights/datatable', [FlightController::class, 'datatable'])->name('flights.datatable');
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
    Route::get('delay/datatable', [DelayController::class, 'datatable'])->name('delay.datatable');
    Route::get('delay/create', [DelayController::class, 'create'])->name('delay.create');
    Route::post('delay', [DelayController::class, 'store'])->name('delay.store');
    Route::get('delay/{delay}/edit', [DelayController::class, 'edit'])->name('delay.edit');
    Route::put('delay/{delay}', [DelayController::class, 'update'])->name('delay.update');
    Route::delete('delay/{delay}', [DelayController::class, 'destroy'])->name('delay.destroy');

    // Master Delay Category
    Route::get('delay-category', [DelayCategoryController::class, 'index'])->name('delay-category.index');
    Route::get('delay-category/datatable', [DelayCategoryController::class, 'datatable'])->name('delay-category.datatable');
    Route::get('delay-category/create', [DelayCategoryController::class, 'create'])->name('delay-category.create');
    Route::post('delay-category', [DelayCategoryController::class, 'store'])->name('delay-category.store');
    Route::get('delay-category/{delayCategory}/edit', [DelayCategoryController::class, 'edit'])->name('delay-category.edit');
    Route::put('delay-category/{delayCategory}', [DelayCategoryController::class, 'update'])->name('delay-category.update');
    Route::delete('delay-category/{delayCategory}', [DelayCategoryController::class, 'destroy'])->name('delay-category.destroy');

    // Master Flight Schedule
    Route::get('flight-schedule', [FlightScheduleController::class, 'index'])->name('flight-schedule.index');
    Route::get('flight-schedule/datatable', [FlightScheduleController::class, 'datatable'])->name('flight-schedule.datatable');
    Route::get('flight-schedule/create', [FlightScheduleController::class, 'create'])->name('flight-schedule.create');
    Route::post('flight-schedule', [FlightScheduleController::class, 'store'])->name('flight-schedule.store');
    Route::get('flight-schedule/{flightSchedule}/edit', [FlightScheduleController::class, 'edit'])->name('flight-schedule.edit');
    Route::put('flight-schedule/{flightSchedule}', [FlightScheduleController::class, 'update'])->name('flight-schedule.update');
    Route::delete('flight-schedule/{flightSchedule}', [FlightScheduleController::class, 'destroy'])->name('flight-schedule.destroy');

    // API auto-fill jadwal
    Route::get('api/flight-schedule/{flightSchedule}', [FlightScheduleController::class, 'getSchedule'])->name('api.flight-schedule');
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User
    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/datatable', [UserController::class, 'datatable'])->name('user.datatable');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

});

require __DIR__.'/auth.php';