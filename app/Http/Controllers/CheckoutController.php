<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Area;
use App\Models\Governorate;
use App\Models\PaymentMethod;
use App\Services\CartService;
use App\Services\DeliverySlotService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $carts,
        protected DeliverySlotService $slots,
        protected OrderService $orders,
    ) {}

    public function index(): Response|RedirectResponse
    {
        $cart = $this->carts->resolve();
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        if (setting('store.closed')) {
            return redirect()->route('home')->with('error', setting('store.closed_message', 'المتجر مقفول مؤقتاً'));
        }

        $summary = $this->carts->summary($cart);
        $availabilities = $cart->items->map(fn ($i) => $i->purchasable->availability_type ?? null)->all();
        $lead = $cart->items->max(fn ($i) => $i->purchasable->lead_time_hours ?? 0);

        return Inertia::render('Checkout/Index', [
            'cart' => $this->carts->toArray($cart),
            'guest_checkout' => (bool) setting('store.guest_checkout', true),
            'governorates' => Governorate::query()->with(['areas' => fn ($q) => $q->where('is_active', true)])->where('is_active', true)->orderBy('sort_order')->get(),
            'addresses' => Auth::check()
                ? Auth::user()->addresses()->with(['governorate', 'area'])->get()
                : [],
            'payment_methods' => PaymentMethod::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'slots' => $this->slots->availableDays($availabilities, 10, $lead ?: null),
            'auth_user' => Auth::user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->carts->resolve();
        abort_if($cart->items->isEmpty(), 422);

        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'address_id' => ['nullable', 'exists:addresses,id'],
            'guest_name' => ['nullable', 'string', 'max:120'],
            'guest_phone' => ['nullable', 'string', 'max:20'],
            'recipient_name' => ['required_without:address_id', 'nullable', 'string', 'max:120'],
            'phone' => ['required_without:address_id', 'nullable', 'string', 'max:20'],
            'governorate_id' => ['required_without:address_id', 'nullable', 'exists:governorates,id'],
            'area_id' => ['required_without:address_id', 'nullable', 'exists:areas,id'],
            'street' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:100'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'],
            'delivery_slot_label' => ['required', 'string'],
            'delivery_slot_start' => ['nullable', 'string'],
            'delivery_slot_end' => ['nullable', 'string'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'accept_terms' => ['accepted'],
        ]);

        if (! empty($data['address_id'])) {
            $address = Address::query()->where('user_id', Auth::id())->findOrFail($data['address_id']);
            $addressData = $address->toSnapshot();
        } else {
            $area = Area::query()->findOrFail($data['area_id']);
            $addressData = [
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'governorate_id' => $data['governorate_id'],
                'area_id' => $data['area_id'],
                'governorate' => $area->governorate?->name_ar,
                'area' => $area->name_ar,
                'street' => $data['street'],
                'building' => $data['building'] ?? null,
                'floor' => $data['floor'] ?? null,
                'apartment' => $data['apartment'] ?? null,
                'landmark' => $data['landmark'] ?? null,
                'delivery_fee' => $area->delivery_fee,
            ];

            if (Auth::check()) {
                Address::query()->create([
                    ...collect($data)->only(['recipient_name', 'phone', 'governorate_id', 'area_id', 'street', 'building', 'floor', 'apartment', 'landmark'])->all(),
                    'user_id' => Auth::id(),
                    'label' => 'البيت',
                    'is_default' => Auth::user()->addresses()->count() === 0,
                ]);
            }
        }

        $method = PaymentMethod::query()->findOrFail($data['payment_method_id']);

        $order = $this->orders->place(
            $cart,
            $method,
            $addressData,
            [
                'date' => $data['delivery_date'],
                'label' => $data['delivery_slot_label'],
                'start' => $data['delivery_slot_start'] ?? null,
                'end' => $data['delivery_slot_end'] ?? null,
            ],
            $data['customer_notes'] ?? null,
            $data['guest_name'] ?? Auth::user()?->name,
            $data['guest_phone'] ?? Auth::user()?->phone ?? $data['phone'] ?? null,
        );

        if ($method->type === 'cash') {
            return redirect()->route('orders.success', $order->number);
        }

        return redirect()->route('orders.pay', $order->number);
    }
}
