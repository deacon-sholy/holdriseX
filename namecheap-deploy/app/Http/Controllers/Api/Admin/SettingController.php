<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const PUBLIC_KEYS = [
        'site_name',
        'site_url',
        'site_description',
        'support_email',
        'contact_phone',
        'company_address',
        'twitter_url',
        'linkedin_url',
        'facebook_url',
        'telegram_url',
        'default_currency',
    ];

    public function index(): JsonResponse
    {
        $settings = Setting::all();

        return response()->json($settings);
    }

    public function publicSettings(): JsonResponse
    {
        $settings = Setting::whereIn('key', self::PUBLIC_KEYS)
            ->pluck('value', 'key');

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
            'settings.*.group_name' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'] ?? null,
                    'group_name' => $item['group_name'] ?? $this->groupForKey($item['key']),
                ]
            );
        }

        return response()->json(['message' => 'Settings updated.']);
    }

    public function show($key): JsonResponse
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        return response()->json($setting);
    }

    private function groupForKey(string $key): string
    {
        if (str_contains($key, 'wallet') || str_contains($key, 'deposit') || str_contains($key, 'withdrawal')) {
            return 'payments';
        }

        if (str_contains($key, 'smtp') || str_contains($key, 'mail') || str_contains($key, 'email')) {
            return 'email';
        }

        if (str_contains($key, 'login') || str_contains($key, 'password') || str_contains($key, 'session') || str_contains($key, 'security')) {
            return 'security';
        }

        if (str_contains($key, 'alert') || str_contains($key, 'notification')) {
            return 'notifications';
        }

        if (str_contains($key, 'trade') || str_contains($key, 'leverage') || str_contains($key, 'spread') || str_contains($key, 'position')) {
            return 'trading';
        }

        return 'general';
    }
}
