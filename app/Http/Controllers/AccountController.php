<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockAlert;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        return Inertia::render('Account/Index', [
            'user' => $user->only(['id', 'name', 'phone', 'email']),
            'stats' => [
                'orders' => $user->orders()->count(),
                'favorites' => $user->favorites()->count(),
            ],
        ]);
    }

    public function orders(Request $request): Response
    {
        $orders = Auth::user()->orders()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->through(fn (Order $o) => [
                'number' => $o->number,
                'status' => $o->status->value,
                'status_label' => $o->status->labelAr(),
                'total' => (float) $o->total,
                'delivery_date' => optional($o->delivery_date)?->toDateString(),
                'placed_at' => optional($o->placed_at)?->toDateString(),
            ]);

        return Inertia::render('Account/Orders', ['orders' => $orders]);
    }

    public function favorites(): Response
    {
        $items = Auth::user()->favorites()->with('product.images')->latest()->get()
            ->map(fn (Favorite $f) => [
                'id' => $f->id,
                'product' => [
                    'id' => $f->product->id,
                    'slug' => $f->product->slug,
                    'name_ar' => $f->product->name_ar,
                    'base_price' => (float) $f->product->base_price,
                    'image' => $f->product->primaryImage()?->path,
                    'availability_type' => $f->product->availability_type->value,
                ],
            ]);

        return Inertia::render('Account/Favorites', ['favorites' => $items]);
    }

    public function toggleFavorite(Product $product): RedirectResponse
    {
        $existing = Favorite::query()->where('user_id', Auth::id())->where('product_id', $product->id)->first();
        if ($existing) {
            $existing->delete();
        } else {
            Favorite::query()->create(['user_id' => Auth::id(), 'product_id' => $product->id]);
        }

        return back();
    }

    public function notifyStock(Product $product): RedirectResponse
    {
        StockAlert::query()->firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'سننبهك عند توفر المنتج.');
    }

    public function profile(): Response
    {
        return Inertia::render('Account/Profile', [
            'user' => Auth::user()->only(['name', 'phone', 'email']),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'] ?? null;
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return back()->with('success', 'تم حفظ بياناتك');
    }

    public function reorder(string $number, CartService $carts): RedirectResponse
    {
        $order = Auth::user()->orders()->where('number', $number)->with('items')->firstOrFail();
        $cart = $carts->resolve();

        foreach ($order->items as $item) {
            if ($item->purchasable_type === Product::class && $item->purchasable) {
                try {
                    $carts->addProduct($cart, $item->purchasable, $item->quantity, null, $item->addons ?? [], $item->customization ?? []);
                } catch (\Throwable) {
                    // skip unavailable
                }
            }
        }

        return redirect()->route('cart.index')->with('success', 'جهّزنا السلة من الطلب السابق');
    }
}
