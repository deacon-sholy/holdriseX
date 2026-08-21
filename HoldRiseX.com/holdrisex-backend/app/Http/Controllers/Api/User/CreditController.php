<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $credits = $request->user()
            ->credits()
            ->latest()
            ->get();

        return response()->json([
            'credits' => $credits,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'purpose' => 'required|string',
            'duration_days' => 'nullable|integer',
        ]);

        $credit = $request->user()->credits()->create([
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'duration_days' => $validated['duration_days'] ?? 30,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Credit application submitted. It will be reviewed by an administrator.',
            'credit' => $credit,
        ], 201);
    }
}
