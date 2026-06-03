<?php
namespace App\Http\Controllers;

use App\Models\DelayCategory;
use Illuminate\Http\Request;

class DelayCategoryController extends Controller
{
    public function index()
    {
        $categories = DelayCategory::withCount('delayCodes')->orderBy('name')->get();
        return view('delay-category.index', compact('categories'));
    }

    public function create()
    {
        return view('delay-category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:delay_categories,name',
        ]);

        DelayCategory::create($request->only('name'));

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
            'name' => 'required|string|max:100|unique:delay_categories,name,' . $delayCategory->id,
        ]);

        $delayCategory->update($request->only('name'));

        return redirect()->route('delay-category.index')
                         ->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(DelayCategory $delayCategory)
    {
        $delayCategory->delete();

        return redirect()->route('delay-category.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }
}