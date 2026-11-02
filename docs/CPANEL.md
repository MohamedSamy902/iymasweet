# دليل النشر على cPanel — Iyma Sweet

## المتطلبات
- PHP 8.2+ (يفضّل 8.3) مع: bcmath, ctype, curl, dom, fileinfo, gd/imagick, intl, json, mbstring, openssl, pdo_mysql, tokenizer, xml, zip
- MySQL
- Cron Job
- **بدون** Redis / Supervisor / Node على السيرفر / WebSocket

## البناء محلياً قبل الرفع
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

ارفع:
- كل ملفات المشروع (ما عدا `node_modules`)
- أو ارفع `vendor/` جاهز لو مفيش SSH/Composer على السيرفر
- لازم يكون موجود: `public/build`

## هيكل المجلدات على cPanel
```
/home/USER/
  iyma-app/          ← كود Laravel (core)
  public_html/       ← محتويات مجلد public/
```

عدّل `public_html/index.php` عشان يشير لمسار `iyma-app`:
```php
require __DIR__.'/../iyma-app/vendor/autoload.php';
$app = require_once __DIR__.'/../iyma-app/bootstrap/app.php';
```

## `.env` للإنتاج
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_LOCALE=ar
APP_TIMEZONE=Africa/Cairo

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=file
FILESYSTEM_DISK=local
```

## أوامر بعد الرفع
```bash
php artisan migrate --force
php artisan db:seed --force   # أول مرة فقط
php artisan storage:link      # أو اضبط public disk على public_html/storage لو symlink ممنوع
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Cron واحد
```
* * * * * /usr/local/bin/php /home/USER/iyma-app/artisan schedule:run >> /dev/null 2>&1
```

الـ schedule بيشغّل كل دقيقة:
- تحديث حالات المنتجات
- إلغاء الطلبات منتهية مهلة الدفع
- `queue:work --stop-when-empty` (database driver)

## الأدمن
- `/admin`
- بعد الـ seed: `admin@iymasweet.com` / `password` — **غيّر الباسورد فوراً**

## ملاحظات أمان
- الإيصالات على disk محلي خاص (`storage/app/private`) مش public
- `APP_DEBUG=false`
- HTTPS إجباري عبر `.htaccess`
