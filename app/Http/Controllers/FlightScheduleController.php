<?php
namespace App\Http\Controllers;

use App\Models\FlightSchedule;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FlightScheduleController extends Controller
{
    public function index()
    {
        return view('flight-schedule.index');
    }

    public function datatable()
    {
        $query = FlightSchedule::select(['id', 'flight_number', 'sta', 'std']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('flight_number_badge', fn($row) =>
                '<span class="badge bg-label-primary fs-6">' . e($row->flight_number) . '</span>'
            )
            ->addColumn('aksi', fn($row) =>
                '<a href="' . route('flight-schedule.edit', $row->id) . '" class="btn btn-sm btn-icon btn-outline-primary">
                    <i class="ri ri-edit-line"></i>
                </a>
                <button type="button"
                    class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                    data-url="' . route('flight-schedule.destroy', $row->id) . '"
                    data-message="Jadwal ' . e($row->flight_number) . ' akan dihapus permanen!">
                    <i class="ri ri-delete-bin-line"></i>
                </button>'
            )
            ->rawColumns(['flight_number_badge', 'aksi'])
            ->make(true);
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
        return request()->expectsJson()
            ? response()->json(['message' => 'Jadwal ' . $flightSchedule->flight_number . ' berhasil dihapus.'])
            : redirect()->route('flight-schedule.index')->with('success', 'Jadwal berhasil dihapus!');
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