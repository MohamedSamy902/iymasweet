<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(string $slug): Response
    {
        $page = Page::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return Inertia::render('Static/Page', [
            'page' => [
                'slug' => $page->slug,
                'title_ar' => $page->title_ar,
                'title_en' => $page->title_en,
                'content_ar' => $page->content_ar,
                'content_en' => $page->content_en,
            ],
            'faqs' => $slug === 'faq'
                ? Faq::query()->where('is_active', true)->orderBy('sort_order')->get()
                : [],
        ]);
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Store as activity / mail log — free SMTP optional
        logger()->info('contact_form', $data);

        return back()->with('success', 'تم استلام رسالتك. سنرد عليك قريباً.');
    }
}
