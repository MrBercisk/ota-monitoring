<?php
namespace App\Http\Controllers;

use App\Models\DelayCategory;
use App\Models\DelayCode;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DelayController extends Controller
{
       public function index()
    {
        return view('delay.index');
    }
 
    public function datatable()
    {
        $delay_codes = DelayCode::with('category')
            ->select(['id', 'code', 'reason', 'delay_category_id']);
 
        return DataTables::of($delay_codes)
            ->addIndexColumn()
            ->addColumn('code_badge', function ($delay) {
                return '<span class="badge bg-label-primary fs-6">' . e($delay->code) . '</span>';
            })
            ->addColumn('category_name', function ($delay) {
                return '<span class="badge bg-label-info rounded-pill">'
                    . e($delay->category->name ?? '-')
                    . '</span>';
            })
           ->addColumn('aksi', function ($delay) {
                $edit = '<a href="' . route('delay.edit', $delay) . '"
                            class="btn btn-sm btn-icon btn-outline-primary">
                            <i class="ri ri-edit-line"></i>
                        </a>';

                $delete = '<button type="button"
                                class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                data-url="' . route('delay.destroy', $delay) . '"
                                data-message="Delay Code ' . e($delay->code) . ' akan dihapus permanen!">
                                <i class="ri ri-delete-bin-line"></i>
                        </button>';

                return $edit . ' ' . $delete;
            })
            ->rawColumns(['code_badge', 'category_name', 'aksi'])
            ->make(true);
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
        $delay->delete();
        return request()->expectsJson()
            ? response()->json(['message' => 'Delay code berhasil dihapus.'])
            : redirect()->route('delay.index')->with('success', 'Delay code berhasil dihapus!');
    }
}