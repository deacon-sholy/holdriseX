<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tickets = $request->user()
            ->tickets()
            ->with('replies')
            ->latest()
            ->get();

        return response()->json([
            'tickets' => $tickets,
            'balance' => $request->user()->balance,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'category' => 'required|string',
            'priority' => 'nullable|string|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $ticket = $request->user()->tickets()->create([
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'] ?? 'medium',
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Ticket submitted successfully. Our support team will respond shortly.',
            'ticket' => $ticket,
        ], 201);
    }

    public function reply(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = $request->user()
            ->tickets()
            ->findOrFail($id);

        $reply = $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_admin' => false,
        ]);

        return response()->json([
            'message' => 'Reply added successfully.',
            'reply' => $reply,
        ], 201);
    }
}
