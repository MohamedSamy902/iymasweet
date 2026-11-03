# Iyma Sweet

متجر حلويات منزلي للسوق المصري — عربي RTL + إنجليزي، جاهز لـ **cPanel** (بدون Redis/WebSocket/Node على السيرفر).

## Stack
Laravel 13 · Inertia + Vue 3 · Tailwind 4 · Filament 4 · MySQL/SQLite · FCM-ready · PWA · Rule-based Chatbot

## تشغيل محلي
```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

| | |
|---|---|
| المتجر | http://127.0.0.1:8000 |
| الأدمن | http://127.0.0.1:8000/admin |
| أدمن | `admin@iymasweet.com` / `password` |
| عميل تجريبي | هاتف `01012345678` / `password` |
| كوبون | `IYMA10` |

```bash
php artisan test
```

## المميزات المنفّذة
- حالات المنتج الأربع + فلاتر ديناميكية + باكدجات
- سلة / Checkout (ضيف أو مسجّل) / مناطق وslots
- كاش + InstaPay + محفظة مع رفع إيصال ومراجعة أدمن
- دورة حالات الطلب + Timeline + جدول تجهيز
- حساب عميل، مفضلة، تنبيه توفر
- CMS: صفحات، FAQ، أقسام رئيسية، نصوص UI، إعدادات/ألوان
- شات بوت rule-based يبحث ويضيف للسلة
- PWA (manifest + service worker)
- إشعارات database + هيكل FCM
- Scheduled commands مناسبة لـ cPanel cron

## النشر
شوف [docs/CPANEL.md](docs/CPANEL.md)

## المراحل
التفاصيل في `docs/PHASE1.md` والبرومت الأصلي. المشروع مكتمل عبر المراحل 1→11 في هذا الريبو.
