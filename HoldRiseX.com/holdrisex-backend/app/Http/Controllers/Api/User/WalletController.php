<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        return response()->json([
            'wallet' => $wallet,
        ]);
    }

    public function connect(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address' => 'required|string',
            'network' => 'required|in:ethereum,bsc,polygon,solana',
            'type' => 'required|in:metamask,trustwallet,coinbase,phantom,other',
        ]);

        $wallet = $request->user()->wallet()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'address' => $validated['address'],
                'network' => $validated['network'],
                'wallet_type' => $validated['type'],
                'connected_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'wallet' => $wallet,
        ]);
    }

    public function disconnect(Request $request): JsonResponse
    {
        Wallet::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
