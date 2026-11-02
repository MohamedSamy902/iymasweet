# المرحلة 1 — الأساس (مكتملة)

## ما تم

1. مشروع Laravel 13 + Inertia + Vue 3 + Tailwind 4 + Vite
2. Filament 4 على `/admin` بـ guard منفصل `admin` (جدول `admins`)
3. جداول `settings` و`ui_texts` + helpers `setting()` و`t()`
4. Design Tokens تُحقن في `:root` من السيرفر (قابلة للتغيير من الداشبورد بدون rebuild)
5. Layout عربي RTL + خطوط self-hosted في `public/fonts`
6. الصفحة الرئيسية Inertia بنصوص من `ui_texts`
7. صفحات الداشبورد: الإعدادات + مدير نصوص الواجهة
8. Seeders للأدمن والإعدادات والنصوص

## قرارات موثّقة

- Laravel **13** (أحدث مستقر ≥ 12 حسب البرومت)
- Filament **4**
- SQLite محلياً للتطوير السريع؛ MySQL للإنتاج على cPanel
- افتراضات القسم 13 من البرومت معتمدة كما هي

## إزاي تجرّب

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

| ماذا | الرابط / الخطوة |
| --- | --- |
| الصفحة الرئيسية RTL | http://127.0.0.1:8000 |
| تبديل اللغة | زر EN أعلى اليمين |
| دخول الأدمن | http://127.0.0.1:8000/admin/login |
| بيانات الأدمن | `admin@iymasweet.com` / `password` |
| تغيير لون Primary | النظام → الإعدادات → تبويب المظهر → احفظ → حدّث الموقع |
| تعديل نص زر | المحتوى → نصوص الواجهة → عدّل inline |
| الاختبارات | `php artisan test` |

## التالي

المرحلة 2: كل الـ migrations والموديلز والعلاقات والـ factories والـ seeders حسب القسم 5.
