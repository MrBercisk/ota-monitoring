<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Station;
use App\Models\DelayCode;
use Illuminate\Http\Request;
use App\Exports\OtaExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $stations = Station::all();
        $query = Flight::with('station');

        if ($request->station_id) {
            $query->where('station_id', $request->station_id);
        }
        if ($request->date_from) {
            $query->whereDate('flight_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('flight_date', '<=', $request->date_to);
        }

        $flights = $query->orderBy('flight_date', 'desc')
                        ->orderBy('sta')
                        ->paginate(20);

        return view('flights.index', compact('flights', 'stations'));
    }

    public function create()
    {
        $stations   = Station::all();
        $delayCodes = DelayCode::with('category')->orderBy('code')->get();
        return view('flights.create', compact('stations', 'delayCodes'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'flight_date'   => 'required|date',
            'flight_number' => 'required|string',
            'station_id'    => 'required|exists:stations,id',
            'sta'           => 'required',
            'std'           => 'required',
        ]);

        $delayMinutes = Flight::calculateDelay(
            $request->sta, 
            $request->ata
        );

        $status = Flight::determineStatus(
            $delayMinutes, 
            $request->remarks
        );

        Flight::create([
            'flight_date'   => $request->flight_date,
            'flight_number' => $request->flight_number,
            'station_id'    => $request->station_id,
            'sta'           => $request->sta,
            'std'           => $request->std,
            'ata'           => $request->ata,
            'atd'           => $request->atd,
            'delay_minutes' => $delayMinutes,
            'delay_code'    => $request->delay_code,
            'status'        => $status,
            'remarks'       => $request->remarks,
        ]);

        return redirect()->route('flights.index')
                        ->with('success', 'Data penerbangan berhasil disimpan!');
    }

    public function edit(Flight $flight)
    {
        $stations   = Station::all();
        $delayCodes = DelayCode::with('category')->orderBy('code')->get();
        return view('flights.edit', compact('flight', 'stations', 'delayCodes'));
    }

    public function update(Request $request, Flight $flight)
    {
        $delayMinutes = Flight::calculateDelay(
            $request->sta, 
            $request->ata
        );

        $status = Flight::determineStatus(
            $delayMinutes, 
            $request->remarks
        );

        $flight->update([
            'flight_date'   => $request->flight_date,
            'flight_number' => $request->flight_number,
            'station_id'    => $request->station_id,
            'sta'           => $request->sta,
            'std'           => $request->std,
            'ata'           => $request->ata,
            'atd'           => $request->atd,
            'delay_minutes' => $delayMinutes,
            'delay_code'    => $request->delay_code,
            'status'        => $status,
            'remarks'       => $request->remarks,
        ]);

        return redirect()->route('flights.index')
                        ->with('success', 'Data berhasil diupdate!');
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();
        return redirect()->route('flights.index')
                        ->with('success', 'Data berhasil dihapus!');
    }
    public function exportWeekly(Request $request)
    {
        $request->validate([
            'week_date' => 'required|date',   // tanggal mana saja dalam minggu itu
        ]);
    
        $date      = Carbon::parse($request->week_date);
        $dateFrom  = $date->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $dateTo    = $date->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
        $stationId = $request->station_id;
    
        $filename = 'OTA_Weekly_' . $dateFrom . '_to_' . $dateTo . '.xlsx';
    
        return Excel::download(
            new OtaExport('weekly', $dateFrom, $dateTo, $stationId),
            $filename
        );
    }
    
    public function exportMonthly(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',   // format: 2025-05
        ]);
    
        $date      = Carbon::createFromFormat('Y-m', $request->month);
        $dateFrom  = $date->startOfMonth()->format('Y-m-d');
        $dateTo    = $date->copy()->endOfMonth()->format('Y-m-d');
        $stationId = $request->station_id;
    
        $filename = 'OTA_Monthly_' . $date->format('F_Y') . '.xlsx';
    
        return Excel::download(
            new OtaExport('monthly', $dateFrom, $dateTo, $stationId),
            $filename
        );
    }
 

}