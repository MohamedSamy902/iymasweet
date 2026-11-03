<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PwaController extends Controller
{
    public function manifest(): JsonResponse
    {
        $name = (string) setting('general.brand_name', 'Iyma Sweet');
        $theme = (string) setting('theme.primary-soft', '#FFF5F8');
        $bg = (string) setting('theme.cream', '#FFFAF6');

        return response()->json([
            'name' => $name,
            'short_name' => mb_substr($name, 0, 12),
            'id' => '/',
            'start_url' => '/?source=pwa',
            'scope' => '/',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'lang' => 'ar',
            'dir' => 'rtl',
            'theme_color' => $theme,
            'background_color' => $bg,
            'icons' => [
                ['src' => '/images/logo-placeholder.svg', 'sizes' => 'any', 'type' => 'image/svg+xml', 'purpose' => 'any'],
                ['src' => '/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => '/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
            'shortcuts' => [
                ['name' => 'المنيو', 'url' => '/shop'],
                ['name' => 'السلة', 'url' => '/cart'],
                ['name' => 'طلباتي', 'url' => '/account/orders'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    public function firebaseConfig(): JsonResponse
    {
        return response()->json([
            'apiKey' => setting('firebase.api_key'),
            'authDomain' => setting('firebase.auth_domain'),
            'projectId' => setting('firebase.project_id'),
            'messagingSenderId' => setting('firebase.messaging_sender_id'),
            'appId' => setting('firebase.app_id'),
            'vapidKey' => setting('firebase.vapid_key'),
        ]);
    }

    public function offline(): Response
    {
        return response()->view('offline');
    }
}
