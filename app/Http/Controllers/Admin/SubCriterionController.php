<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\SubCriterion;
use Illuminate\Http\Request;

class SubCriterionController extends Controller
{
    public function index(Request $request)
    {
        $selectedCriterionId = $request->query('criterion_id');
        $criteria = Criterion::orderBy('code')->get();

        if (!$selectedCriterionId && $criteria->count() > 0) {
            $selectedCriterionId = $criteria->first()->id;
        }

        $selectedCriterion = Criterion::with('subCriteria')->find($selectedCriterionId);

        return view('admin.sub_criteria.index', compact('criteria', 'selectedCriterion'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'criterion_id' => 'required|exists:criteria,id',
            'name' => 'required|string|max:255',
            'operator' => 'required|in:>,>=,<,<=,=,between,equals_text',
            'min_val' => 'nullable|numeric',
            'max_val' => 'nullable|numeric',
            'text_value' => 'nullable|string',
            'utility_value' => 'required|numeric|min:0|max:100',
        ]);

        SubCriterion::create($validated);

        return redirect()->route('admin.sub-criteria.index', ['criterion_id' => $request->criterion_id])
            ->with('success', 'Sub Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, SubCriterion $subCriterion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'operator' => 'required|in:>,>=,<,<=,=,between,equals_text',
            'min_val' => 'nullable|numeric',
            'max_val' => 'nullable|numeric',
            'text_value' => 'nullable|string',
            'utility_value' => 'required|numeric|min:0|max:100',
        ]);

        $subCriterion->update($validated);

        return redirect()->route('admin.sub-criteria.index', ['criterion_id' => $subCriterion->criterion_id])
            ->with('success', 'Sub Kriteria berhasil diperbarui.');
    }

    public function destroy(SubCriterion $subCriterion)
    {
        $criterionId = $subCriterion->criterion_id;
        $subCriterion->delete();

        return redirect()->route('admin.sub-criteria.index', ['criterion_id' => $criterionId])
            ->with('success', 'Sub Kriteria berhasil dihapus.');
    }
}
