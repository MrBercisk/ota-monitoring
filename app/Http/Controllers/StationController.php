<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Station;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StationController extends Controller
{
    public function index()
    {
        return view('stations.index');
    }
    
    public function datatable()
    {
        $stations = Station::withCount('flights')
            ->select(['id', 'code', 'name']);
    
        return DataTables::of($stations)
            ->addIndexColumn()
            ->addColumn('code_badge', function ($station) {
                return '<span class="badge bg-label-primary fs-6">' . e($station->code) . '</span>';
            })
            ->addColumn('flights_badge', function ($station) {
                return '<span class="badge bg-label-info rounded-pill">'
                    . $station->flights_count . ' penerbangan'
                    . '</span>';
            })
            ->addColumn('aksi', function ($station) {
                $edit = '<a href="' . route('stations.edit', $station) . '"
                            class="btn btn-sm btn-icon btn-outline-primary">
                            <i class="ri ri-edit-line"></i>
                        </a>';

                $delete = '<button type="button"
                                class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                data-url="' . route('stations.destroy', $station) . '"
                                data-message="Station ' . e($station->code) . ' akan dihapus permanen!">
                                <i class="ri ri-delete-bin-line"></i>
                        </button>';

                return $edit . ' ' . $delete;
            })
            ->rawColumns(['code_badge', 'flights_badge', 'aksi'])
            ->make(true);
    }

      public function create()
    {
        return view('stations.create');
    }
     public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:stations',
            'name' => 'required|string|max:100',
        ]);

        Station::create($request->only('code', 'name'));

        return redirect()->route('stations.index')
                        ->with('success', 'Station berhasil ditambahkan!');
    }
      public function edit(Station $station)
    {
        return view('stations.edit', compact('station'));
    }

    public function update(Request $request, Station $station)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:stations,code,' . $station->id,
            'name' => 'required|string|max:100',
        ]);

        $station->update($request->only('code', 'name'));

        return redirect()->route('stations.index')
                        ->with('success', 'Station berhasil diupdate!');
    }

    public function destroy(Station $station)
    {
        $station->delete();
        return request()->expectsJson()
            ? response()->json(['message' => 'Station ' . $station->code . ' berhasil dihapus.'])
            : redirect()->route('stations.index')->with('success', 'Station berhasil dihapus!');
    }

  
}