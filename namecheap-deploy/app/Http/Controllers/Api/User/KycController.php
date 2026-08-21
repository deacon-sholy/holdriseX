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
            'front_image' => 'nullable|string|required_without:document_front',
            'back_image' => 'nullable|string|required_without:document_back',
            'document_front' => 'nullable|file|image|max:5120|required_without:front_image',
            'document_back' => 'nullable|file|image|max:5120|required_without:back_image',
        ]);

        $user = $request->user();

        $hasPending = $user->kycDocuments()
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();

        if ($hasPending) {
            return response()->json(['message' => 'You already have a pending KYC submission.'], 422);
        }

        $frontImage = $validated['front_image'] ?? $this->storeUploadedDocument($request, 'document_front', 'front');
        $backImage = $validated['back_image'] ?? $this->storeUploadedDocument($request, 'document_back', 'back');

        $document = $user->kycDocuments()->create([
            'document_type' => $validated['document_type'],
            'front_image' => $frontImage,
            'back_image' => $backImage,
            'status' => 'pending',
        ]);

        $user->update(['kyc_status' => 'pending']);

        return response()->json([
            'message' => 'KYC documents submitted for review.',
            'document' => $document,
        ], 201);
    }

    private function storeUploadedDocument(Request $request, string $field, string $side): string
    {
        $file = $request->file($field);
        $directory = public_path('uploads/kyc');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $request->user()->id.'_'.time().'_'.$side.'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/kyc/'.$filename;
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
