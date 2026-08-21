<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = KycDocument::with('user');

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $documents = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($documents);
    }

    public function show($id): JsonResponse
    {
        $document = KycDocument::with('user')->findOrFail($id);

        return response()->json($document);
    }

    public function approve($id): JsonResponse
    {
        $document = KycDocument::findOrFail($id);

        if ($document->status !== 'pending' && $document->status !== 'under_review') {
            return response()->json(['message' => 'KYC document is not pending review.'], 422);
        }

        $document->update([
            'status' => 'verified',
            'reviewed_at' => now(),
        ]);

        $document->user->update(['kyc_status' => 'verified']);

        return response()->json([
            'message' => 'KYC document approved.',
            'document' => $document->fresh()->load('user'),
        ]);
    }

    public function reject($id): JsonResponse
    {
        $document = KycDocument::findOrFail($id);

        if ($document->status !== 'pending' && $document->status !== 'under_review') {
            return response()->json(['message' => 'KYC document is not pending review.'], 422);
        }

        $document->update([
            'status' => 'rejected',
            'admin_note' => request()->input('reason', request()->input('admin_note', 'Documents do not meet requirements.')),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'KYC document rejected.',
            'document' => $document->fresh()->load('user'),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => KycDocument::count(),
            'pending' => KycDocument::where('status', 'pending')->count(),
            'under_review' => KycDocument::where('status', 'under_review')->count(),
            'verified' => KycDocument::where('status', 'verified')->count(),
            'rejected' => KycDocument::where('status', 'rejected')->count(),
        ]);
    }
}
