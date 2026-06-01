<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Station;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $totalFlights  = Flight::whereBetween('flight_date', [$startOfMonth, $endOfMonth])->count();
        $onTime        = Flight::whereBetween('flight_date', [$startOfMonth, $endOfMonth])->where('status', 'on_time')->count();
        $delayed       = Flight::whereBetween('flight_date', [$startOfMonth, $endOfMonth])->where('status', 'delayed')->count();
        $totalStations = Station::count();
        $otaPercentage = $totalFlights > 0 ? round(($onTime / $totalFlights) * 100) : 0;

        // Rekap per station
        $stationStats = Station::all()->map(function ($station) use ($startOfMonth, $endOfMonth) {
            $flights   = Flight::where('station_id', $station->id)
                               ->whereBetween('flight_date', [$startOfMonth, $endOfMonth])
                               ->get();
            $total     = $flights->count();
            $onTime    = $flights->where('status', 'on_time')->count();
            $delayed   = $flights->where('status', 'delayed')->count();
            $nightStop = $flights->where('status', 'night_stop')->count();
            $pct       = $total > 0 ? round(($onTime / $total) * 100) : 0;

            return [
                'code'       => $station->code,
                'name'       => $station->name,
                'total'      => $total,
                'on_time'    => $onTime,
                'delayed'    => $delayed,
                'night_stop' => $nightStop,
                'percentage' => $pct,
            ];
        })->filter(fn($s) => $s['total'] > 0)->values();

        return view('dashboard', compact(
            'totalFlights', 'onTime', 'delayed',
            'totalStations', 'otaPercentage', 'stationStats'
        ));
    }
}