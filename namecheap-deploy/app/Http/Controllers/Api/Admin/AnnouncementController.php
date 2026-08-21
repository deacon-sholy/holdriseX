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
            $query->where('status', $status);
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
            'type' => 'nullable|in:info,warning,maintenance,promotion',
            'target_audience' => 'nullable|in:all,specific_group',
            'status' => 'nullable|in:published,draft,scheduled',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $validated['type'] = $validated['type'] ?? 'info';
        $validated['target_audience'] = $validated['target_audience'] ?? 'all';
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['admin_id'] = $request->user()->id;

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
            'type' => 'sometimes|in:info,warning,maintenance,promotion',
            'target_audience' => 'sometimes|in:all,specific_group',
            'status' => 'sometimes|in:published,draft,scheduled',
            'scheduled_at' => 'nullable|date|after:now',
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
            'status' => 'published',
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
            'published' => Announcement::where('status', 'published')->count(),
            'draft' => Announcement::where('status', 'draft')->count(),
            'scheduled' => Announcement::where('status', 'scheduled')->count(),
        ]);
    }
}
