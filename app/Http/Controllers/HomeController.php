<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home', [
            'howSteps' => [
                ['icon' => 'donut', 'title_key' => 'home.step_pick', 'fallback' => 'اختاري المنتج'],
                ['icon' => 'calendar', 'title_key' => 'home.step_slot', 'fallback' => 'احجزي ميعاد'],
                ['icon' => 'wallet', 'title_key' => 'home.step_pay', 'fallback' => 'ادفعي براحتك'],
                ['icon' => 'bike', 'title_key' => 'home.step_receive', 'fallback' => 'استلمي الطلب'],
            ],
        ]);
    }
}
