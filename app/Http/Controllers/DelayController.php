<?php
namespace App\Http\Controllers;

use App\Models\DelayCategory;
use App\Models\DelayCode;
use Illuminate\Http\Request;

class DelayController extends Controller
{
    public function index(Request $request)
    {
        $delay_codes = DelayCode::with('category')->get();
        return view('delay.index', compact('delay_codes'));
    }

    public function create()
    {
        $categories = DelayCategory::orderBy('name')->get();
        return view('delay.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delay_code'        => 'required|string|max:2|unique:delay_codes,code',
            'reason'            => 'required|string|max:100',
            'delay_category_id' => 'required|exists:delay_categories,id',
        ]);

            DelayCode::create([
            'code'              => $request->delay_code,
            'reason'            => $request->reason,
            'delay_category_id' => $request->delay_category_id,
        ]);


        return redirect()->route('delay.index')
                        ->with('success', 'Delay code berhasil ditambahkan!');
    }
    public function edit(DelayCode $delay)
    {
        $categories = DelayCategory::orderBy('name')->get();
        return view('delay.edit', compact('delay', 'categories'));
    }


    public function update(Request $request, DelayCode $delay)
    {
        $request->validate([
            'delay_code'        => 'required|string|max:2|unique:delay_codes,code,' . $delay->id,
            'reason'            => 'required|string|max:100',
            'delay_category_id' => 'required|exists:delay_categories,id',
        ]);

        $delay->update([
            'code'              => $request->delay_code,
            'reason'            => $request->reason,
            'delay_category_id' => $request->delay_category_id,
        ]);

        return redirect()->route('delay.index')
                        ->with('success', 'Delay code berhasil diupdate!');
    }
    public function destroy(DelayCode $delay)
    {
        // dd($delay);
        $delay->delete();

        return redirect()->route('delay.index')
                         ->with('success', 'Delay code berhasil dihapus!');
    }
}