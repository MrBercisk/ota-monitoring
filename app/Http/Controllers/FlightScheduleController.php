<?php
namespace App\Http\Controllers;

use App\Models\FlightSchedule;
use Illuminate\Http\Request;

class FlightScheduleController extends Controller
{
    public function index()
    {
        $schedules = FlightSchedule::orderBy('flight_number')->get();
        return view('flight-schedule.index', compact('schedules'));
    }

    public function create()
    {
        return view('flight-schedule.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'flight_number' => 'required|string|max:20|unique:flight_schedules,flight_number',
            'sta'           => 'required',
            'std'           => 'required',
        ]);

        FlightSchedule::create($request->only('flight_number', 'sta', 'std'));

        return redirect()->route('flight-schedule.index')
                         ->with('success', 'Jadwal penerbangan berhasil ditambahkan!');
    }

    public function edit(FlightSchedule $flightSchedule)
    {
        return view('flight-schedule.edit', compact('flightSchedule'));
    }

    public function update(Request $request, FlightSchedule $flightSchedule)
    {
        $request->validate([
            'flight_number' => 'required|string|max:20|unique:flight_schedules,flight_number,' . $flightSchedule->id,
            'sta'           => 'required',
            'std'           => 'required',
        ]);

        $flightSchedule->update($request->only('flight_number', 'sta', 'std'));

        return redirect()->route('flight-schedule.index')
                         ->with('success', 'Jadwal penerbangan berhasil diupdate!');
    }

    public function destroy(FlightSchedule $flightSchedule)
    {
        $flightSchedule->delete();
        return redirect()->route('flight-schedule.index')
                         ->with('success', 'Jadwal penerbangan berhasil dihapus!');
    }

    // API untuk auto-fill STA & STD
    public function getSchedule(FlightSchedule $flightSchedule)
    {
        return response()->json([
            'sta' => $flightSchedule->sta,
            'std' => $flightSchedule->std,
        ]);
    }
}