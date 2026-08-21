<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'sent' => $user->sentTransfers()->latest()->get(),
            'received' => $user->receivedTransfers()->latest()->get(),
            'balance' => $user->balance,
            'stats' => [
                'total_sent' => (float) $user->sentTransfers()->where('status', 'completed')->sum('amount'),
                'total_received' => (float) $user->receivedTransfers()->where('status', 'completed')->sum('amount'),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient' => 'required|string',
            'amount' => 'required|numeric|min:10',
            'note' => 'nullable|string',
        ]);

        $user = $request->user();

        $identifier = trim($validated['recipient']);
        $recipient = User::where('email', $identifier)->first();

        if (! $recipient && ctype_digit($identifier)) {
            $recipient = User::find($identifier);
        }

        if (! $recipient) {
            return response()->json(['message' => 'Recipient not found.'], 422);
        }

        if ($recipient->id === $user->id) {
            return response()->json(['message' => 'You cannot transfer funds to yourself.'], 422);
        }

        if ((float) $validated['amount'] > (float) $user->balance) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        $transfer = DB::transaction(function () use ($user, $recipient, $validated) {
            $user->decrement('balance', $validated['amount']);
            $recipient->increment('balance', $validated['amount']);

            return Transfer::create([
                'sender_id' => $user->id,
                'recipient_id' => $recipient->id,
                'recipient_identifier' => $validated['recipient'],
                'amount' => $validated['amount'],
                'note' => $validated['note'] ?? null,
                'status' => 'completed',
            ]);
        });

        return response()->json([
            'message' => 'Transfer completed successfully.',
            'transfer' => $transfer,
        ], 201);
    }
}
