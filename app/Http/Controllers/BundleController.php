<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use Inertia\Inertia;
use Inertia\Response;

class BundleController extends Controller
{
    public function index(): Response
    {
        $bundles = Bundle::query()->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Bundle $b) => [
            'slug' => $b->slug,
            'name_ar' => $b->name_ar,
            'name_en' => $b->name_en,
            'price' => (float) $b->price,
            'compare_at_price' => $b->compare_at_price ? (float) $b->compare_at_price : null,
            'image' => $b->image,
            'type' => $b->type,
            'availability_type' => $b->availability_type->value,
        ]);

        return Inertia::render('Shop/Bundles', ['bundles' => $bundles]);
    }

    public function show(string $slug): Response
    {
        $bundle = Bundle::query()->with(['items.product.images', 'allowedProducts.images'])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return Inertia::render('Shop/BundleShow', [
            'bundle' => [
                'id' => $bundle->id,
                'slug' => $bundle->slug,
                'name_ar' => $bundle->name_ar,
                'description_ar' => $bundle->description_ar,
                'price' => (float) $bundle->price,
                'compare_at_price' => $bundle->compare_at_price ? (float) $bundle->compare_at_price : null,
                'image' => $bundle->image,
                'type' => $bundle->type,
                'min_items' => $bundle->min_items,
                'max_items' => $bundle->max_items,
                'items' => $bundle->items,
                'allowed_products' => $bundle->allowedProducts,
                'availability_type' => $bundle->availability_type->value,
            ],
        ]);
    }
}
