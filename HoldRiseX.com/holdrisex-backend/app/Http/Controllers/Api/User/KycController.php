<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $documents = $request->user()
            ->kycDocuments()
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($documents);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $document = $request->user()
            ->kycDocuments()
            ->findOrFail($id);

        return response()->json($document);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|in:id_card,passport,drivers_license',
            'front_image' => 'required|string',
            'back_image' => 'required|string',
        ]);

        $user = $request->user();

        $hasPending = $user->kycDocuments()
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();

        if ($hasPending) {
            return response()->json(['message' => 'You already have a pending KYC submission.'], 422);
        }

        $document = $user->kycDocuments()->create([
            'document_type' => $validated['document_type'],
            'front_image' => $validated['front_image'],
            'back_image' => $validated['back_image'],
            'status' => 'pending',
        ]);

        $user->update(['kyc_status' => 'pending']);

        return response()->json([
            'message' => 'KYC documents submitted for review.',
            'document' => $document,
        ], 201);
    }

    public function status(Request $request): JsonResponse
    {
        $latestDocument = $request->user()
            ->kycDocuments()
            ->latest()
            ->first();

        return response()->json([
            'kyc_status' => $request->user()->kyc_status,
            'latest_document' => $latestDocument,
        ]);
    }
}
