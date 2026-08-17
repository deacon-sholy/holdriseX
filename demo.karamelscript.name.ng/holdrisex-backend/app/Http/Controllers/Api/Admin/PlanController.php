<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = InvestmentPlan::withCount('investments')->orderBy('sort_order')->get();

        return response()->json($plans);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gte:min_amount',
            'daily_return' => 'required|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1',
            'min_profit' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if (InvestmentPlan::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $validated['slug'] . '-' . time();
        }

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $plan = InvestmentPlan::create($validated);

        return response()->json([
            'message' => 'Plan created.',
            'plan' => $plan,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $plan = InvestmentPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'min_amount' => 'sometimes|numeric|min:0',
            'max_amount' => 'sometimes|numeric|gte:min_amount',
            'daily_return' => 'sometimes|numeric|min:0|max:100',
            'duration_days' => 'sometimes|integer|min:1',
            'min_profit' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $plan->update($validated);

        return response()->json([
            'message' => 'Plan updated.',
            'plan' => $plan->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $plan = InvestmentPlan::findOrFail($id);
        $plan->delete();

        return response()->json(['message' => 'Plan deleted.']);
    }

    public function toggleStatus($id): JsonResponse
    {
        $plan = InvestmentPlan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);

        return response()->json([
            'message' => 'Plan status toggled.',
            'is_active' => $plan->fresh()->is_active,
        ]);
    }
}
