<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orders) {}

    public function success(string $number): Response
    {
        $order = $this->findAccessible($number);

        return Inertia::render('Checkout/Success', [
            'order' => $this->payload($order),
        ]);
    }

    public function pay(string $number): Response
    {
        $order = $this->findAccessible($number);

        return Inertia::render('Checkout/Pay', [
            'order' => $this->payload($order),
            'method' => $order->paymentMethod,
        ]);
    }

    public function uploadReceipt(Request $request, string $number): RedirectResponse
    {
        $order = $this->findAccessible($number);

        $data = $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,heic', 'max:5120'],
            'sender_name' => ['nullable', 'string', 'max:120'],
            'sender_phone_or_reference' => ['nullable', 'string', 'max:120'],
        ]);

        $file = $data['receipt'];
        $hash = hash_file('sha256', $file->getRealPath());
        $duplicate = Payment::query()->where('receipt_hash', $hash)->where('order_id', '!=', $order->id)->exists();
        $path = $file->store('receipts/'.$order->number, 'local');

        $this->orders->submitReceipt(
            $order,
            $path,
            $hash,
            $data['sender_name'] ?? null,
            $data['sender_phone_or_reference'] ?? null,
        );

        return redirect()
            ->route('account.orders.show', $order->number)
            ->with('success', $duplicate
                ? 'تم رفع الإيصال (تنبيه: صورة مشابهة استُخدمت قبل كده)'
                : 'تم رفع الإيصال وسيتم مراجعته قريباً.');
    }

    public function show(string $number): Response
    {
        $order = $this->findAccessible($number)->load(['items', 'statusHistory', 'payments', 'paymentMethod']);

        return Inertia::render('Account/OrderShow', [
            'order' => $this->payload($order),
            'timeline' => $order->statusHistory,
            'payments' => $order->payments,
        ]);
    }

    protected function findAccessible(string $number): Order
    {
        $order = Order::query()->where('number', $number)->firstOrFail();

        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403);
        }

        return $order;
    }

    protected function payload(Order $order): array
    {
        return [
            'id' => $order->id,
            'number' => $order->number,
            'status' => $order->status->value,
            'status_label' => $order->status->labelAr(),
            'payment_status' => $order->payment_status->value,
            'subtotal' => (float) $order->subtotal,
            'discount_total' => (float) $order->discount_total,
            'delivery_fee' => (float) $order->delivery_fee,
            'total' => (float) $order->total,
            'delivery_date' => optional($order->delivery_date)?->toDateString(),
            'delivery_slot_label' => $order->delivery_slot_label,
            'address_snapshot' => $order->address_snapshot,
            'customer_notes' => $order->customer_notes,
            'payment_due_at' => optional($order->payment_due_at)?->toIso8601String(),
            'payment_method' => $order->paymentMethod,
            'items' => $order->items,
            'placed_at' => optional($order->placed_at)?->toIso8601String(),
        ];
    }
}
