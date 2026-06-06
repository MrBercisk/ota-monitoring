<?php
namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now          = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();
        $today        = $now->toDateString();

        // ── Stat Cards ────────────────────────────────────────────────
        $baseQuery = fn() => Flight::whereBetween('flight_date', [$startOfMonth, $endOfMonth]);

        $totalFlights      = $baseQuery()->count();
        $onTime            = $baseQuery()->where('status', 'on_time')->count();
        $delayed           = $baseQuery()->where('status', 'delayed')->count();
        $totalStations     = Station::count();
        $otaPercentage     = $totalFlights > 0 ? round(($onTime / $totalFlights) * 100) : 0;
        $totalDelayMinutes = $baseQuery()->where('status', 'delayed')->sum('delay_minutes');

        // ── Flight Hari Ini ───────────────────────────────────────────
        $todayQuery   = fn() => Flight::whereDate('flight_date', $today);
        $todayFlights = $todayQuery()->count();
        $todayOnTime  = $todayQuery()->where('status', 'on_time')->count();
        $todayDelayed = $todayQuery()->where('status', 'delayed')->count();

        // ── OTA Trend 30 Hari ─────────────────────────────────────────
        // Ambil semua sekaligus dengan groupBy, lebih efisien dari 30x query
        $trendRaw = Flight::selectRaw('
                flight_date,
                COUNT(*)                                    AS total,
                SUM(status = "on_time")                     AS on_time_count,
                SUM(status = "delayed")                     AS delay_count
            ')
            ->whereBetween('flight_date', [
                $now->copy()->subDays(29)->toDateString(),
                $today,
            ])
            ->groupBy('flight_date')
            ->orderBy('flight_date')
            ->get()
            ->keyBy(fn($r) => Carbon::parse($r->flight_date)->toDateString());

        $trendLabels = [];
        $trendOta    = [];
        $trendDelay  = [];

        for ($i = 29; $i >= 0; $i--) {
            $date          = $now->copy()->subDays($i)->toDateString();
            $row           = $trendRaw->get($date);
            $trendLabels[] = Carbon::parse($date)->translatedFormat('d M');
            $trendOta[]    = ($row && $row->total > 0)
                                ? round(($row->on_time_count / $row->total) * 100)
                                : null;
            $trendDelay[]  = $row ? (int) $row->delay_count : 0;
        }

        // ── Rekap Per Station ─────────────────────────────────────────
        $stationRaw = Flight::whereBetween('flight_date', [$startOfMonth, $endOfMonth])
        ->selectRaw('
            station_id,
            COUNT(*)                        AS total,
            SUM(status = "on_time")         AS on_time,
            SUM(status = "delayed")         AS delayed_count,
            SUM(status = "night_stop")      AS night_stop
        ')
        ->groupBy('station_id')
        ->get()
        ->keyBy('station_id');

    $stationStats = Station::all()->map(function ($station) use ($stationRaw) {
        $row       = $stationRaw->get($station->id);
        $total     = $row ? (int) $row->total         : 0;
        $onTime    = $row ? (int) $row->on_time       : 0;
        $delayed   = $row ? (int) $row->delayed_count : 0;  // pakai alias baru
        $nightStop = $row ? (int) $row->night_stop    : 0;
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

        // ── Top Delay Station (top 5) ─────────────────────────────────
        $topDelayStations = $stationStats->sortByDesc('delayed')->take(5)->values();
        $maxDelayed       = $topDelayStations->max('delayed') ?: 1;
        $topDelayStations = $topDelayStations->map(fn($s) => array_merge($s, [
            'pct_of_max' => round(($s['delayed'] / $maxDelayed) * 100),
        ]))->values();

        // ── Top Delay Code (top 5) ────────────────────────────────────
        // Flight.delay_code_id → delay_codes.id
        // delay_codes.code     = kode IATA (e.g. "93")
        // delay_codes.reason   = deskripsi
        $topDelayCodes = DB::table('flights')
            ->join('delay_codes', 'flights.delay_code_id', '=', 'delay_codes.id')
            ->whereBetween('flights.flight_date', [$startOfMonth, $endOfMonth])
            ->where('flights.status', 'delayed')
            ->whereNotNull('flights.delay_code_id')
            ->selectRaw('
                delay_codes.id,
                delay_codes.code                    AS code,
                delay_codes.reason                  AS label,
                COUNT(*)                            AS total_flights,
                SUM(flights.delay_minutes)          AS total_minutes
            ')
            ->groupBy('delay_codes.id', 'delay_codes.code', 'delay_codes.reason')
            ->orderByDesc('total_minutes')
            ->limit(5)
            ->get();

        $maxMinutes    = $topDelayCodes->max('total_minutes') ?: 1;
        $topDelayCodes = $topDelayCodes->map(fn($c) => [
            'code'          => $c->code,
            'label'         => $c->label,
            'total_flights' => (int) $c->total_flights,
            'total_minutes' => (int) $c->total_minutes,
            'pct_of_max'    => round(($c->total_minutes / $maxMinutes) * 100),
        ])->values();

        return view('dashboard', compact(
            'totalFlights', 'onTime', 'delayed', 'totalStations', 'otaPercentage',
            'totalDelayMinutes',
            'todayFlights', 'todayOnTime', 'todayDelayed',
            'trendLabels', 'trendOta', 'trendDelay',
            'stationStats', 'topDelayStations', 'topDelayCodes'
        ));
    }
}