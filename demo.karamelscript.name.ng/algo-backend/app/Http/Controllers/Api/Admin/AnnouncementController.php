<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::query();

        if ($status = $request->input('status')) {
            $query->where('is_published', $status === 'published');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($announcements);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'nullable|in:info,warning,maintenance,update',
        ]);

        $validated['type'] = $validated['type'] ?? 'info';

        $announcement = Announcement::create($validated);

        return response()->json([
            'message' => 'Announcement created.',
            'announcement' => $announcement,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'type' => 'sometimes|in:info,warning,maintenance,update',
        ]);

        $announcement->update($validated);

        return response()->json([
            'message' => 'Announcement updated.',
            'announcement' => $announcement->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted.']);
    }

    public function publish($id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);

        $announcement->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Announcement published.',
            'announcement' => $announcement->fresh(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => Announcement::count(),
            'published' => Announcement::where('is_published', true)->count(),
            'draft' => Announcement::where('is_published', false)->count(),
        ]);
    }
}
