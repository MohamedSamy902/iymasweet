<?php

namespace Tests\Feature;

use App\Enums\AvailabilityType;
use App\Enums\OrderStatus;
use App\Models\Area;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\ChatbotService;
use App\Services\DeliverySlotService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_shop_and_product_pages_work(): void
    {
        $product = Product::query()->where('availability_type', AvailabilityType::AvailableNow)->firstOrFail();

        $this->get('/shop')->assertOk();
        $this->get('/product/'.$product->slug)->assertOk();
        $this->get('/bundles')->assertOk();
        $this->get('/tomorrow')->assertOk();
    }

    public function test_cart_checkout_cash_order_flow(): void
    {
        $product = Product::query()->where('availability_type', AvailabilityType::AvailableNow)->firstOrFail();
        $user = User::query()->where('phone', '01012345678')->firstOrFail();
        $area = Area::query()->firstOrFail();
        $cash = PaymentMethod::query()->where('type', 'cash')->firstOrFail();

        $this->actingAs($user)
            ->post('/cart', ['product_id' => $product->id, 'quantity' => 2])
            ->assertRedirect();

        $slots = app(DeliverySlotService::class)->availableDays([$product->availability_type]);
        $this->assertNotEmpty($slots);

        $response = $this->actingAs($user)->post('/checkout', [
            'payment_method_id' => $cash->id,
            'recipient_name' => 'سارة',
            'phone' => '01012345678',
            'governorate_id' => $area->governorate_id,
            'area_id' => $area->id,
            'street' => 'شارع تجريبي',
            'delivery_date' => $slots[0]['date'],
            'delivery_slot_label' => $slots[0]['slots'][0]['label'],
            'delivery_slot_start' => $slots[0]['slots'][0]['start'],
            'delivery_slot_end' => $slots[0]['slots'][0]['end'],
            'accept_terms' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => OrderStatus::Confirmed->value,
        ]);
    }

    public function test_chatbot_finds_donuts(): void
    {
        $bot = app(ChatbotService::class);
        $result = $bot->handle('عايز دونتس لوتس');

        $this->assertContains($result['intent'], ['search_product', 'add_to_cart', 'available_now']);
        $this->assertTrue(($result['products'] ?? []) !== [] || str_contains($result['text'] ?? '', 'سلة') || str_contains($result['text'] ?? '', 'ضيف'));
    }

    public function test_coupon_percent_discount(): void
    {
        $product = Product::query()->where('availability_type', AvailabilityType::AvailableNow)->firstOrFail();
        $carts = app(CartService::class);
        $cart = $carts->resolve('test-session');
        $carts->addProduct($cart, $product, 2);
        $carts->applyCoupon($cart->fresh(), 'IYMA10');
        $summary = $carts->summary($cart->fresh(['items', 'coupon']));

        $this->assertGreaterThan(0, $summary['discount']);
        $this->assertSame('IYMA10', $summary['coupon_code']);
    }

    public function test_pwa_manifest_and_chatbot_api(): void
    {
        $this->get('/manifest.webmanifest')->assertOk()->assertJsonPath('lang', 'ar');
        $this->get('/api/chatbot/init')->assertOk()->assertJsonStructure(['session_id', 'welcome']);
        $this->postJson('/api/chatbot/message', ['message' => 'أهلاً'])->assertOk();
    }

    public function test_order_status_machine(): void
    {
        $product = Product::query()->where('availability_type', AvailabilityType::AvailableNow)->firstOrFail();
        $user = User::query()->where('phone', '01012345678')->firstOrFail();
        $area = Area::query()->firstOrFail();
        $cash = PaymentMethod::query()->where('type', 'cash')->firstOrFail();
        $carts = app(CartService::class);
        $cart = $carts->resolve();
        $cart->update(['user_id' => $user->id]);
        $carts->addProduct($cart, $product, 1);
        $slots = app(DeliverySlotService::class)->availableDays([$product->availability_type]);

        $order = app(OrderService::class)->place(
            $cart->fresh(['items.purchasable', 'coupon']),
            $cash,
            ['recipient_name' => 'سارة', 'phone' => '01012345678', 'area_id' => $area->id, 'governorate_id' => $area->governorate_id, 'street' => 'x', 'area' => $area->name_ar, 'delivery_fee' => $area->delivery_fee],
            ['date' => $slots[0]['date'], 'label' => $slots[0]['slots'][0]['label'], 'start' => $slots[0]['slots'][0]['start'], 'end' => $slots[0]['slots'][0]['end']],
            null,
            'سارة',
            '01012345678',
        );

        $order = app(OrderService::class)->changeStatus($order, OrderStatus::Preparing, 'admin', 1);
        $this->assertSame(OrderStatus::Preparing, $order->status);
        $this->assertDatabaseCount('order_status_history', 2);
    }
}
