<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Station;
use App\Models\DelayCode;
use Illuminate\Http\Request;

class DelayController extends Controller
{
    public function index(Request $request)
    {
        $delay_code = DelayCode::all();
        return view('delay.index', compact('delay_code'));
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
        return redirect()->route('stations.index')
                        ->with('success', 'Station berhasil dihapus!');
    }

  
}