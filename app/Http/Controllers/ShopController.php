<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->listing($request);
    }

    public function category(Request $request, string $slug): Response
    {
        $category = Category::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return $this->listing($request, $category);
    }

    public function tomorrow(Request $request): Response
    {
        $request->merge(['availability' => 'available_tomorrow']);

        return $this->listing($request);
    }

    public function preOrder(Request $request): Response
    {
        $request->merge(['availability' => 'pre_order']);

        return $this->listing($request);
    }

    public function search(Request $request): Response
    {
        return $this->listing($request);
    }

    protected function listing(Request $request, ?Category $category = null): Response
    {
        $query = Product::query()
            ->with(['images', 'category', 'variants'])
            ->where('is_active', true);

        if ($category) {
            $query->where('category_id', $category->id);
        }

        if ($request->filled('availability')) {
            $query->where('availability_type', $request->string('availability'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('name_ar', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%")
                    ->orWhere('search_text', 'like', "%{$q}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', (float) $request->input('max_price'));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        if ($request->boolean('new')) {
            $query->where('is_new', true);
        }
        if ($request->boolean('best_seller')) {
            $query->where('is_best_seller', true);
        }
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('compare_at_price');
        }

        if ($request->filled('attribute')) {
            foreach ((array) $request->input('attribute') as $attributeId => $valueId) {
                $query->whereHas('attributeValues', function ($q) use ($attributeId, $valueId) {
                    $q->where('attribute_id', $attributeId)->where('attribute_value_id', $valueId);
                });
            }
        }

        $sort = $request->string('sort', 'available_first');
        match ((string) $sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'rating' => $query->orderByDesc('avg_rating'),
            'bestseller' => $query->orderByDesc('is_best_seller')->orderByDesc('ratings_count'),
            'latest' => $query->latest(),
            default => $query->orderByRaw("CASE availability_type WHEN 'sold_out' THEN 1 ELSE 0 END")->orderBy('sort_order'),
        };

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('Shop/Index', [
            'products' => $products->through(fn (Product $p) => $this->card($p)),
            'filters' => $request->only(['q', 'availability', 'sort', 'min_price', 'max_price', 'featured', 'new', 'best_seller', 'on_sale', 'attribute']),
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'slug', 'name_ar', 'name_en', 'color', 'image']),
            'attributes' => Attribute::query()->with('values')->where('is_filterable', true)->orderBy('sort_order')->get(),
            'category' => $category ? [
                'slug' => $category->slug,
                'name_ar' => $category->name_ar,
                'name_en' => $category->name_en,
            ] : null,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::query()
            ->with(['images', 'variants', 'addonGroups.options', 'category', 'tags', 'reviews' => fn ($q) => $q->where('status', 'approved')->latest()->limit(10)])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $product->increment('views_count');

        $related = Product::query()
            ->with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get()
            ->map(fn (Product $p) => $this->card($p));

        return Inertia::render('Shop/Show', [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name_ar' => $product->name_ar,
                'name_en' => $product->name_en,
                'short_description_ar' => $product->short_description_ar,
                'description_ar' => $product->description_ar,
                'base_price' => (float) $product->base_price,
                'compare_at_price' => $product->compare_at_price ? (float) $product->compare_at_price : null,
                'availability_type' => $product->availability_type->value,
                'availability_label' => $product->availability_type->labelAr(),
                'lead_time_hours' => $product->lead_time_hours,
                'stock_quantity' => $product->stock_quantity,
                'min_qty' => $product->min_qty,
                'max_qty_per_order' => $product->max_qty_per_order,
                'allow_customization' => $product->allow_customization,
                'customization_config' => $product->customization_config,
                'ingredients_ar' => $product->ingredients_ar,
                'allergens_ar' => $product->allergens_ar,
                'storage_instructions_ar' => $product->storage_instructions_ar,
                'avg_rating' => (float) $product->avg_rating,
                'ratings_count' => $product->ratings_count,
                'images' => $product->images,
                'variants' => $product->variants,
                'addon_groups' => $product->addonGroups,
                'category' => $product->category,
                'reviews' => $product->reviews->load('user:id,name'),
            ],
            'related' => $related,
        ]);
    }

    protected function card(Product $p): array
    {
        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'name_ar' => $p->name_ar,
            'name_en' => $p->name_en,
            'base_price' => (float) $p->base_price,
            'compare_at_price' => $p->compare_at_price ? (float) $p->compare_at_price : null,
            'availability_type' => $p->availability_type->value,
            'availability_label' => $p->availability_type->labelAr(),
            'avg_rating' => (float) $p->avg_rating,
            'image' => $p->primaryImage()?->path ?? '/images/logo-placeholder.svg',
            'is_new' => $p->is_new,
            'is_best_seller' => $p->is_best_seller,
            'category' => $p->category?->name_ar,
        ];
    }
}
