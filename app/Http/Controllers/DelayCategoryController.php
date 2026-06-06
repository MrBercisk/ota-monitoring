<?php
namespace App\Http\Controllers;

use App\Models\DelayCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DelayCategoryController extends Controller
{
    public function index()
    {
        return view('delay-category.index');
    }

    public function datatable()
    {
        $query = DelayCategory::withCount('delayCodes')->orderBy('name');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('delay_codes_count', fn($row) => 
                '<span class="badge bg-label-secondary">' . $row->delay_codes_count . ' kode</span>'
            )
            ->addColumn('aksi', fn($row) =>
                '<a href="' . route('delay-category.edit', $row->id) . '" class="btn btn-sm btn-icon btn-outline-primary">
                    <i class="ri ri-edit-line"></i>
                </a>
                <button type="button"
                    class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                    data-url="' . route('delay-category.destroy', $row->id) . '"
                    data-message="Delay Category ' . e($row->name) . ' akan dihapus permanen!">
                    <i class="ri ri-delete-bin-line"></i>
                </button>'
            )
            ->rawColumns(['delay_codes_count', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('delay-category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:delay_categories,name',
        ]);
        
        DelayCategory::create([
            'name'        => $request->category_name
        ]);


        return redirect()->route('delay-category.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(DelayCategory $delayCategory)
    {
        return view('delay-category.edit', compact('delayCategory'));
    }

    public function update(Request $request, DelayCategory $delayCategory)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:delay_categories,name,' . $delayCategory->id,
        ]);

        $delayCategory->update($request->only('name'));
        $delayCategory->update([
            'name'        => $request->category_name,
        ]);

        return redirect()->route('delay-category.index')
                         ->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(DelayCategory $delayCategory)
    {
        $delayCategory->delete();
        return request()->expectsJson()
            ? response()->json(['message' => 'Kategori berhasil dihapus.'])
            : redirect()->route('delay-category.index')->with('success', 'Kategori berhasil dihapus!');
    }
}