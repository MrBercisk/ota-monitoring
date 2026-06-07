<?php
namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $today = Carbon::today();
        $notifications = collect();

        // ── 1. Flight delay hari ini ──
        $delayedFlights = Flight::with(['station', 'delayCode'])
            ->whereDate('flight_date', $today)
            ->where('status', 'delayed')
            ->orderByDesc('delay_minutes')
            ->take(5)
            ->get();

        foreach ($delayedFlights as $flight) {
            $notifications->push([
                'id'      => 'delay_' . $flight->id,
                'type'    => 'delay',
                'title'   => 'Flight Delay — ' . $flight->flight_number,
                'message' => ($flight->station->code ?? '-') . ' · Delay ' . $flight->delay_minutes . ' menit'
                           . ($flight->delayCode ? ' (' . $flight->delayCode->code . ')' : ''),
                'time'    => $flight->updated_at,
                'is_read' => false,
            ]);
        }

        // ── 2. OTA harian di bawah target ──
        $totalToday = Flight::whereDate('flight_date', $today)
            ->whereNotIn('status', ['noop', 'night_stop'])
            ->count();

        if ($totalToday > 0) {
            $onTimeToday = Flight::whereDate('flight_date', $today)
                ->where('status', 'on_time')
                ->count();

            $otaPercent = round(($onTimeToday / $totalToday) * 100, 1);

            if ($otaPercent < 80) {
                $notifications->push([
                    'id'      => 'ota_' . $today->format('Ymd'),
                    'type'    => 'ota_low',
                    'title'   => 'OTA Hari Ini Rendah',
                    'message' => 'OTA saat ini ' . $otaPercent . '% dari ' . $totalToday . ' penerbangan — di bawah target 80%',
                    'time'    => now(),
                    'is_read' => false,
                ]);
            }
        }

        return response()->json([
            'notifications' => $notifications->values(),
            'unread_count'  => $notifications->count(),
        ]);
    }
}