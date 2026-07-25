<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::with('subCriteria')->orderBy('code')->get();
        $totalWeight = $criteria->where('is_active', true)->sum('weight');
        return view('admin.criteria.index', compact('criteria', 'totalWeight'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        Criterion::create($validated);

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria baru berhasil ditambahkan.');
    }

    public function update(Request $request, Criterion $criterion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $criterion->update($validated);

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Criterion $criterion)
    {
        $criterion->delete();
        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }
}
