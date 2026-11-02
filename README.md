# Iyma Sweet

متجر إلكتروني لبراند حلويات منزلي **Iyma Sweet** (*Handmade with love*) — عربي RTL + إنجليزي، موجّه للسوق المصري، جاهز للنشر على **cPanel** بدون Redis/WebSocket/Node على السيرفر.

## Stack

- Laravel 13 (PHP 8.3+) + MySQL/SQLite
- Inertia.js + Vue 3 (`<script setup>`) + Tailwind CSS 4 + Vite
- Filament 4 على `/admin` (guard منفصل `admin`)
- Queue/Session: database · Cache: file

## المرحلة الحالية

**المرحلة 1 — الأساس** مكتملة:

- سقالات المشروع + Design Tokens (CSS variables من جدول `settings`)
- نظام `settings` + `ui_texts` + helper `t()` / `setting()`
- Layout عربي RTL + خطوط self-hosted (Cairo / Baloo Bhaijaan 2 / Fredoka)
- Filament: دخول الأدمن، صفحة الإعدادات، مدير نصوص الواجهة
- الصفحة الرئيسية Inertia مع نصوص من الداشبورد

## التشغيل المحلي

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # أو اضبط MySQL في .env
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

- الموقع: http://127.0.0.1:8000
- الداشبورد: http://127.0.0.1:8000/admin  
  - الإيميل: `admin@iymasweet.com`  
  - الباسورد: `password`

للتطوير مع HMR:

```bash
composer run dev
# أو
php artisan serve & npm run dev
```

## أوامر مفيدة

```bash
php artisan test
php artisan db:seed
php artisan optimize:clear
```

## ملاحظات cPanel (لاحقاً)

- ابنِ الـ assets محلياً (`npm run build`) وارفع `public/build`
- التطبيق خارج `public_html` ومحتويات `public/` داخله
- Cron واحد: `* * * * * php /path/to/artisan schedule:run`
- Drivers: `QUEUE_CONNECTION=database`, `SESSION_DRIVER=database`, `CACHE_STORE=file`

## ترتيب المراحل القادمة

2. قاعدة البيانات الكاملة (منتجات، طلبات، …)  
3. الداشبورد الأساسي للكتالوج  
4. واجهة الكتالوج للعميل  
5. السلة والحسابات والـ Checkout  
6. الدفع ودورة الطلب  
7. Firebase  
8. PWA  
9. الشات بوت  
10. التقييمات/CMS/SEO  
11. الأمان والاختبارات ودليل النشر
