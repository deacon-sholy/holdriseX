<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use Illuminate\Http\JsonResponse;

class SignalController extends Controller
{
    public function index(): JsonResponse
    {
        $signals = Signal::latest()->get();

        return response()->json($signals);
    }
}
