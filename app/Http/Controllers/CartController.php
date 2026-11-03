<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(protected CartService $carts) {}

    public function index(): Response
    {
        $cart = $this->carts->resolve();

        return Inertia::render('Cart/Index', [
            'cart' => $this->carts->toArray($cart),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'bundle_id' => ['nullable', 'exists:bundles,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'addons' => ['nullable', 'array'],
            'customization' => ['nullable', 'array'],
        ]);

        $cart = $this->carts->resolve();

        if (! empty($data['bundle_id'])) {
            $this->carts->addBundle($cart, Bundle::query()->findOrFail($data['bundle_id']), $data['quantity'] ?? 1, $data['customization'] ?? []);
        } else {
            $this->carts->addProduct(
                $cart,
                Product::query()->findOrFail($data['product_id']),
                $data['quantity'] ?? 1,
                $data['variant_id'] ?? null,
                $data['addons'] ?? [],
                $data['customization'] ?? [],
            );
        }

        return back()->with('success', t('product.added_toast', 'تمت الإضافة إلى السلة'));
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $cart = $this->carts->resolve();
        abort_unless($item->cart_id === $cart->id, 403);

        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:50']]);
        $this->carts->updateQuantity($item, $data['quantity']);

        return back();
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $cart = $this->carts->resolve();
        abort_unless($item->cart_id === $cart->id, 403);
        $this->carts->removeItem($item);

        return back();
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:40']]);
        $this->carts->applyCoupon($this->carts->resolve(), $data['code']);

        return back()->with('success', 'تم تطبيق الكوبون');
    }
}
