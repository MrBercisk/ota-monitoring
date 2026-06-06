<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Station;
use App\Models\DelayCode;
use Illuminate\Http\Request;
use App\Exports\OtaExport;
use App\Models\FlightSchedule;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $stations = Station::all();
        return view('flights.index', compact('stations'));
    }

    public function datatable(Request $request)
    {
        $query = Flight::with(['station', 'delayCode'])
            ->select(['id', 'flight_date', 'flight_number', 'station_id', 'sta', 'std', 'ata', 'atd', 'delay_minutes', 'status', 'delay_code_id']);

        if ($request->station_id) {
            $query->where('station_id', $request->station_id);
        }
        if ($request->date_from) {
            $query->whereDate('flight_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('flight_date', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', fn($row) =>
                $row->flight_date->format('d/m/Y')
            )
            ->addColumn('station_badge', fn($row) =>
                '<span class="badge bg-label-primary">' . e($row->station->code ?? '-') . '</span>'
            )
            ->addColumn('delay_badge', fn($row) =>
                $row->delay_minutes > 0
                    ? '<span class="badge bg-danger">' . $row->delay_minutes . ' mnt</span>'
                    : '<span class="badge bg-success">0</span>'
            )
            ->addColumn('status_badge', function ($row) {
                return match($row->status) {
                    'on_time'    => '<span class="badge bg-success">ON TIME</span>',
                    'delayed'    => '<span class="badge bg-danger">DELAYED</span>',
                    default      => '<span class="badge bg-secondary">NIGHT STOP</span>',
                };
            })
            ->addColumn('aksi', fn($row) =>
                '<a href="' . route('flights.edit', $row->id) . '" class="btn btn-sm btn-icon btn-outline-primary">
                    <i class="ri ri-edit-line"></i>
                </a>
                <button type="button"
                    class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                    data-url="' . route('flights.destroy', $row->id) . '"
                    data-message="Data penerbangan ' . e($row->flight_number) . ' tanggal ' . $row->flight_date->format('d/m/Y') . ' akan dihapus permanen!">
                    <i class="ri ri-delete-bin-line"></i>
                </button>'
            )
            ->rawColumns(['station_badge', 'delay_badge', 'status_badge', 'aksi'])
            ->filterColumn('tanggal', fn($query, $keyword) =>
                $query->whereRaw('DATE_FORMAT(flight_date, "%d/%m/%Y") like ?', ["%{$keyword}%"])
            )
            ->make(true);
    }
    public function create()
    {
        $stations        = Station::all();
        $delayCodes      = DelayCode::with('category')->orderBy('code')->get();
        $flightSchedules = FlightSchedule::orderBy('flight_number')->get();
        return view('flights.create', compact('stations', 'delayCodes', 'flightSchedules'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'flight_schedule_id' => 'required|exists:flight_schedules,id',
            'flight_date'        => 'required|date',
            'station_id'         => 'required|exists:stations,id',
            'sta'                => 'required',
            'std'                => 'required',
        ]);

        $delayMinutes = Flight::calculateDelay($request->sta, $request->ata);
        $status       = Flight::determineStatus($delayMinutes, $request->remarks);

        $schedule = FlightSchedule::find($request->flight_schedule_id);

        Flight::create([
            'flight_date'        => $request->flight_date,
            'flight_number'      => $schedule->flight_number,
            'flight_schedule_id' => $request->flight_schedule_id,
            'station_id'         => $request->station_id,
            'sta'                => $request->sta,
            'std'                => $request->std,
            'ata'                => $request->ata,
            'atd'                => $request->atd,
            'delay_minutes'      => $delayMinutes,
            'delay_code_id'      => $request->delay_code_id,
            'status'             => $status,
            'remarks'            => $request->remarks,
        ]);

        return redirect()->route('flights.index')
                        ->with('success', 'Data penerbangan berhasil disimpan!');
    }

    public function edit(Flight $flight)
    {
        $stations        = Station::all();
        $delayCodes      = DelayCode::with('category')->orderBy('code')->get();
        $flightSchedules = FlightSchedule::orderBy('flight_number')->get();
        return view('flights.edit', compact('flight', 'stations', 'delayCodes', 'flightSchedules'));
    }

    public function update(Request $request, Flight $flight)
    {
        $delayMinutes = Flight::calculateDelay($request->sta, $request->ata);
        $status       = Flight::determineStatus($delayMinutes, $request->remarks);

        $schedule = FlightSchedule::find($request->flight_schedule_id);

        $flight->update([
            'flight_date'        => $request->flight_date,
            'flight_number'      => $schedule->flight_number,
            'flight_schedule_id' => $request->flight_schedule_id,
            'station_id'         => $request->station_id,
            'sta'                => $request->sta,
            'std'                => $request->std,
            'ata'                => $request->ata,
            'atd'                => $request->atd,
            'delay_minutes'      => $delayMinutes,
            'delay_code_id'      => $request->delay_code_id,
            'status'             => $status,
            'remarks'            => $request->remarks,
        ]);

        return redirect()->route('flights.index')
                        ->with('success', 'Data berhasil diupdate!');
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();
        return request()->expectsJson()
            ? response()->json(['message' => 'Data penerbangan ' . $flight->flight_number . ' berhasil dihapus.'])
            : redirect()->route('flights.index')->with('success', 'Data berhasil dihapus!');
    }
    public function exportWeekly(Request $request)
    {
        $request->validate([
            'week_date' => 'required|date',
        ]);

        $date     = Carbon::parse($request->week_date);
        $dateFrom = $date->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $dateTo   = $date->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');

        $filename = 'OTA_Weekly_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new OtaExport('weekly', $dateFrom, $dateTo),
            $filename
        );
    }

    public function exportMonthly(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $date     = Carbon::createFromFormat('Y-m', $request->month);
        $dateFrom = $date->startOfMonth()->format('Y-m-d');
        $dateTo   = $date->copy()->endOfMonth()->format('Y-m-d');

        $filename = 'OTA_Monthly_' . $date->format('F_Y') . '.xlsx';

        return Excel::download(
            new OtaExport('monthly', $dateFrom, $dateTo),
            $filename
        );
    }
    

}