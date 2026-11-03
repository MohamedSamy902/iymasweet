<?php

namespace App\Services;

use App\Enums\AvailabilityType;
use App\Models\ChatbotIntent;
use App\Models\ChatbotLog;
use App\Models\ChatbotSynonym;
use App\Models\ChatSession;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChatbotService
{
    public function __construct(
        protected CartService $carts,
    ) {}

    public function init(?string $sessionId = null): array
    {
        $session = $this->session($sessionId);

        return [
            'session_id' => $session->session_id,
            'welcome' => t('home.welcome', 'أهلاً بيك في Iyma Sweet'),
            'disclaimer' => t('chat.subtitle', 'مساعد تلقائي للإجابة السريعة'),
            'quick_replies' => [
                ['label' => 'المتاح الآن', 'message' => 'إيه المتاح دلوقتي؟'],
                ['label' => 'بحث عن منتج', 'message' => 'عايز دونتس'],
                ['label' => 'تتبع طلب', 'message' => 'طلبي فين؟'],
                ['label' => 'طرق الدفع', 'message' => 'طرق الدفع'],
            ],
        ];
    }

    public function handle(string $message, ?string $sessionId = null, ?int $userId = null): array
    {
        $session = $this->session($sessionId, $userId);
        $normalized = $this->normalize($message);
        $expanded = $this->expandSynonyms($normalized);
        [$intent, $score] = $this->detectIntent($expanded);
        $entities = $this->extractEntities($expanded);

        $response = match ($intent) {
            'greeting' => $this->textResponse('أهلاً بيك. قولي عايزة تبحثي عن إيه أو اختاري من الأزرار.'),
            'available_now' => $this->productsByAvailability(AvailabilityType::AvailableNow),
            'available_tomorrow' => $this->productsByAvailability(AvailabilityType::AvailableTomorrow),
            'preorder' => $this->productsByAvailability(AvailabilityType::PreOrder),
            'search_product' => $this->searchProducts($expanded, $entities),
            'show_cart' => $this->showCart(),
            'add_to_cart' => $this->addFromMessage($expanded, $entities),
            'track_order' => $this->trackOrder($expanded, $userId),
            'delivery_info' => $this->textResponse('التوصيل متاح لمناطق القاهرة والجيزة المفعّلة. سعر التوصيل يظهر تلقائياً عند اختيار المنطقة في إتمام الطلب.'),
            'payment_methods' => $this->textResponse('نقبل كاش عند الاستلام، InstaPay، والمحافظ الإلكترونية. بعد التحويل ارفعي صورة الإيصال من صفحة الطلب.'),
            'working_hours' => $this->textResponse('مواعيد التوصيل حسب جدول العمل اليومي. اختاري اليوم والموعد المناسب أثناء إتمام الطلب.'),
            'offers' => $this->textResponse('العروض والكوبونات المتاحة تظهر في الصفحة الرئيسية والمنيو.'),
            'faq' => $this->faqAnswer($expanded),
            'human_contact' => $this->textResponse('للتواصل المباشر عبر واتساب: '.setting('general.whatsapp', '201000000000')),
            'thanks', 'bye' => $this->textResponse('على الرحب. لو احتجتي حاجة تانية إحنا موجودين.'),
            default => $this->fallback($expanded),
        };

        ChatbotLog::query()->create([
            'session_id' => $session->session_id,
            'user_id' => $userId,
            'message' => $message,
            'detected_intent' => $intent,
            'confidence' => $score,
            'understood' => $intent !== 'fallback',
            'meta' => ['entities' => $entities],
        ]);

        $session->update([
            'context' => array_merge($session->context ?? [], ['last_intent' => $intent, 'last_entities' => $entities]),
            'last_activity' => now(),
        ]);

        return array_merge($response, [
            'session_id' => $session->session_id,
            'intent' => $intent,
            'confidence' => $score,
        ]);
    }

    public function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text) ?? $text;
        $text = str_replace(['أ', 'إ', 'آ', 'ى', 'ة', 'ؤ', 'ئ'], ['ا', 'ا', 'ا', 'ي', 'ه', 'و', 'ي'], $text);
        $text = strtr($text, ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);
        $text = preg_replace('/(.)\\1{2,}/u', '$1$1', $text) ?? $text;
        $stop = ['من', 'الى', 'إلى', 'على', 'في', 'عن', 'او', 'أو', 'يا', 'لو', 'عايز', 'عايزة', 'عاوز', 'ممكن', 'من فضلك'];
        $parts = preg_split('/\s+/u', $text) ?: [];
        $parts = array_values(array_filter($parts, fn ($p) => $p !== '' && ! in_array($p, $stop, true)));

        return implode(' ', $parts);
    }

    protected function expandSynonyms(string $text): string
    {
        $synonyms = ChatbotSynonym::query()->get();
        foreach ($synonyms as $row) {
            $text = preg_replace('/\b'.preg_quote($row->synonym, '/').'\b/u', $row->canonical, $text) ?? $text;
        }

        return $text;
    }

    /** @return array{0: string, 1: float} */
    protected function detectIntent(string $text): array
    {
        $intents = ChatbotIntent::query()->where('is_active', true)->orderByDesc('priority')->get();
        $best = 'fallback';
        $bestScore = 0.0;

        foreach ($intents as $intent) {
            $score = 0.0;
            foreach ($intent->keywords ?? [] as $keyword => $weight) {
                if (is_int($keyword)) {
                    $keyword = (string) $weight;
                    $weight = 1;
                }
                if ($keyword !== '' && str_contains($text, $this->normalize((string) $keyword))) {
                    $score += (float) $weight;
                }
            }
            foreach ($intent->patterns ?? [] as $pattern) {
                if (@preg_match('/'.$pattern.'/u', $text)) {
                    $score += 2;
                }
            }
            $score += ($intent->priority / 100);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $intent->key;
            }
        }

        if ($bestScore < 1) {
            return ['fallback', $bestScore];
        }

        return [$best, $bestScore];
    }

    protected function extractEntities(string $text): array
    {
        $entities = [];
        if (preg_match('/(?:تحت|اقل من|أقل من)\s*(\d+)/u', $text, $m)) {
            $entities['max_price'] = (int) $m[1];
        }
        if (preg_match('/(\d+)\s*(قطعه|قطعة|قطع|دونت|دونتس)?/u', $text, $m)) {
            $entities['quantity'] = (int) $m[1];
        }
        foreach (['شوكولاته', 'فانيليا', 'فراوله', 'لوتس', 'نوتيلا'] as $flavor) {
            if (str_contains($text, $flavor)) {
                $entities['flavor'] = $flavor;
            }
        }

        return $entities;
    }

    protected function searchProducts(string $text, array $entities): array
    {
        $query = Product::query()->with('images')->where('is_active', true);

        $tokens = array_filter(explode(' ', $text));
        $query->where(function ($q) use ($tokens) {
            foreach ($tokens as $token) {
                if (mb_strlen($token) < 2) {
                    continue;
                }
                $q->orWhere('search_text', 'like', "%{$token}%")
                    ->orWhere('name_ar', 'like', "%{$token}%")
                    ->orWhere('name_en', 'like', "%{$token}%");
            }
        });

        if (isset($entities['max_price'])) {
            $query->where('base_price', '<=', $entities['max_price']);
        }

        $products = $query->orderByRaw("CASE availability_type WHEN 'sold_out' THEN 1 ELSE 0 END")
            ->limit(5)
            ->get();

        if ($products->isEmpty()) {
            return $this->textResponse('مفيش نتائج مطابقة. جرّبي كلمة تانية أو اختاري «المتاح الآن».', suggestions: true);
        }

        return [
            'type' => 'products',
            'text' => 'نتائج البحث:',
            'products' => $products->map(fn (Product $p) => $this->productCard($p))->values(),
            'buttons' => [
                ['label' => 'المزيد', 'message' => 'المزيد'],
                ['label' => 'السلة', 'message' => 'السله'],
            ],
        ];
    }

    protected function productsByAvailability(AvailabilityType $type): array
    {
        $products = Product::query()->with('images')->where('is_active', true)->where('availability_type', $type)->limit(5)->get();

        return [
            'type' => 'products',
            'text' => $type->labelAr(),
            'products' => $products->map(fn (Product $p) => $this->productCard($p))->values(),
            'buttons' => [],
        ];
    }

    protected function addFromMessage(string $text, array $entities): array
    {
        $product = Product::query()->where('is_active', true)
            ->where(function ($q) use ($text) {
                foreach (explode(' ', $text) as $token) {
                    if (mb_strlen($token) > 2) {
                        $q->orWhere('name_ar', 'like', "%{$token}%");
                    }
                }
            })->first();

        if (! $product) {
            return $this->searchProducts($text, $entities);
        }

        if ($product->allow_customization) {
            return $this->textResponse('هذا المنتج يحتاج تخصيص. افتحي صفحة المنتج لإضافة النص أو الصورة.', buttons: [
                ['label' => 'فتح المنتج', 'url' => '/product/'.$product->slug],
            ]);
        }

        $cart = $this->carts->resolve();
        $qty = (int) ($entities['quantity'] ?? 1);
        $this->carts->addProduct($cart, $product, $qty);

        return $this->textResponse("تمت إضافة {$product->name_ar} إلى السلة.", buttons: [
            ['label' => 'إتمام الطلب', 'url' => '/checkout'],
            ['label' => 'السلة', 'url' => '/cart'],
            ['label' => 'إضافة منتج آخر', 'message' => 'عايز حاجه تانيه'],
        ]);
    }

    protected function showCart(): array
    {
        $cart = $this->carts->toArray($this->carts->resolve());

        return [
            'type' => 'cart',
            'text' => $cart['summary']['count']
                ? "{$cart['summary']['count']} منتجات — الإجمالي {$cart['summary']['total']} ج.م"
                : 'السلة فارغة حالياً.',
            'cart' => $cart,
            'buttons' => [
                ['label' => 'إتمام الطلب', 'url' => '/checkout'],
            ],
        ];
    }

    protected function trackOrder(string $text, ?int $userId): array
    {
        if ($userId) {
            $orders = Order::query()->where('user_id', $userId)->latest()->limit(3)->get();

            return [
                'type' => 'orders',
                'text' => 'آخر طلباتك:',
                'orders' => $orders->map(fn (Order $o) => [
                    'number' => $o->number,
                    'status' => $o->status->labelAr(),
                    'total' => (float) $o->total,
                    'url' => '/account/orders/'.$o->number,
                ])->values(),
            ];
        }

        if (preg_match('/IS-\d{2}-\d+/i', $text, $m)) {
            $order = Order::query()->where('number', strtoupper($m[0]))->first();
            if ($order) {
                return $this->textResponse("طلب {$order->number}: {$order->status->labelAr()}", buttons: [
                    ['label' => 'التفاصيل', 'url' => '/account/orders/'.$order->number],
                ]);
            }
        }

        return $this->textResponse('أرسلي رقم الطلب (مثال IS-26-000001) مع آخر 4 أرقام من الموبايل، أو سجّلي الدخول.');
    }

    protected function faqAnswer(string $text): array
    {
        $faq = Faq::query()->where('is_active', true)->get()->first(function (Faq $f) use ($text) {
            return str_contains($this->normalize($f->question_ar), $text)
                || collect(explode(' ', $text))->contains(fn ($t) => $t && str_contains($this->normalize($f->question_ar), $t));
        });

        if (! $faq) {
            return $this->textResponse('راجعي صفحة الأسئلة الشائعة، أو اطلبي التواصل مع فريق الدعم.', buttons: [
                ['label' => 'الأسئلة الشائعة', 'url' => '/faq'],
            ]);
        }

        return $this->textResponse($faq->answer_ar);
    }

    protected function fallback(string $text): array
    {
        return $this->textResponse('لم أتمكن من فهم الطلب. اختاري من الأزرار أو اكتبي اسم المنتج.', suggestions: true);
    }

    protected function textResponse(string $text, array $buttons = [], bool $suggestions = false): array
    {
        if ($suggestions && $buttons === []) {
            $buttons = [
                ['label' => 'المتاح الآن', 'message' => 'إيه المتاح دلوقتي؟'],
                ['label' => 'تواصل', 'message' => 'عايز اكلم حد'],
            ];
        }

        return ['type' => 'text', 'text' => $text, 'buttons' => $buttons, 'products' => []];
    }

    protected function productCard(Product $p): array
    {
        return [
            'id' => $p->id,
            'name' => $p->name_ar,
            'price' => (float) $p->base_price,
            'slug' => $p->slug,
            'image' => $p->primaryImage()?->path,
            'availability' => $p->availability_type->value,
            'availability_label' => $p->availability_type->labelAr(),
            'url' => '/product/'.$p->slug,
        ];
    }

    protected function session(?string $sessionId = null, ?int $userId = null): ChatSession
    {
        $sessionId ??= (string) Str::uuid();

        return ChatSession::query()->firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => $userId, 'context' => [], 'last_activity' => now()]
        );
    }
}
