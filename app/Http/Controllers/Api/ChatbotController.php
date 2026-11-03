<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ChatbotController extends Controller
{
    public function __construct(protected ChatbotService $bot) {}

    public function init(Request $request): JsonResponse
    {
        return response()->json($this->bot->init($request->string('session_id')));
    }

    public function message(Request $request): JsonResponse
    {
        $key = 'chatbot:'.($request->session()->getId() ?: $request->ip());
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['text' => 'استني لحظة وجرّبي تاني ⏳', 'type' => 'text'], 429);
        }
        RateLimiter::hit($key, 60);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'session_id' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json(
            $this->bot->handle($data['message'], $data['session_id'] ?? null, $request->user()?->id)
        );
    }
}
