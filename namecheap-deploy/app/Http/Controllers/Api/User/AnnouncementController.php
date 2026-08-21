<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::where('status', 'published');

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $announcements = $query->latest()
            ->select('id', 'title', 'content', 'type', 'status', 'created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json($announcements);
    }

    public function show($id): JsonResponse
    {
        $announcement = Announcement::where('status', 'published')
            ->select('id', 'title', 'content', 'type', 'status', 'created_at')
            ->findOrFail($id);

        $announcement->increment('views_count');

        return response()->json($announcement);
    }
}
