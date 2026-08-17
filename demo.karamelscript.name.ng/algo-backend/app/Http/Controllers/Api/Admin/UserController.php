<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('is_active', $status === 'active');
        }

        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $allowedSorts = ['name', 'email', 'balance', 'created_at', 'is_active', 'role', 'kyc_status'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $users = $query->paginate($request->input('per_page', 15));

        return response()->json($users);
    }

    public function show($id): JsonResponse
    {
        $user = User::with(['investments', 'trades', 'deposits', 'withdrawals'])->findOrFail($id);

        return response()->json($user);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'balance' => 'nullable|numeric|min:0',
            'role' => 'nullable|in:user,admin',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['role'] = $validated['role'] ?? 'user';

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'balance' => 'nullable|numeric|min:0',
            'role' => 'sometimes|in:user,admin',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user->fresh(),
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => 'User status toggled.',
            'is_active' => $user->fresh()->is_active,
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'pending_kyc' => User::where('kyc_status', 'pending')->count(),
            'suspended' => User::where('is_active', false)->count(),
        ]);
    }
}
