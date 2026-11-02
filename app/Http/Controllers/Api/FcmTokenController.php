<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:512'],
            'platform' => ['nullable', 'string', 'max:20'],
        ]);

        FcmToken::query()->updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id' => $request->user()?->id,
                'platform' => $data['platform'] ?? 'web',
                'user_agent' => (string) $request->userAgent(),
                'last_used_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate(['token' => ['required', 'string']]);
        FcmToken::query()->where('token', $data['token'])->delete();

        return response()->json(['ok' => true]);
    }
}
