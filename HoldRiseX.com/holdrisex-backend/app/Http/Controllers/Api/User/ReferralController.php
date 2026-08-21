<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (empty($user->referral_code)) {
            $user->update(['referral_code' => strtoupper(\Illuminate\Support\Str::random(8))]);
            $user->refresh();
        }

        $referredUsers = User::where('referred_by', $user->id)
            ->select('id', 'name', 'email', 'created_at')
            ->get()
            ->map(function ($ref) use ($user) {
                $totalDeposits = $ref->deposits()->where('status', 'completed')->sum('amount');
                return [
                    'id' => $ref->id,
                    'name' => $ref->name,
                    'email' => $ref->email,
                    'created_at' => $ref->created_at,
                    'deposit_amount' => (float) $totalDeposits,
                    'commission_earned' => round($totalDeposits * 0.05, 2),
                ];
            });

        $totalEarned = $referredUsers->sum('commission_earned');

        return response()->json([
            'referral_code' => $user->referral_code,
            'referral_link' => request()->getSchemeAndHttpHost() . '/register?ref=' . $user->referral_code,
            'total_referrals' => $referredUsers->count(),
            'total_earned' => $totalEarned,
            'referrals' => $referredUsers,
            'balance' => (float) $user->balance,
        ]);
    }
}
