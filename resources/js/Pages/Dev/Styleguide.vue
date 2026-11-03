<script setup>
import { Head } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import Sparkle from '@/Components/Ui/Sparkle.vue';
import StatusBadge from '@/Components/Ui/StatusBadge.vue';
import WaveDivider from '@/Components/Ui/WaveDivider.vue';

defineOptions({ layout: ShopLayout });
</script>

<template>
    <Head title="Sweet Boutique Styleguide" />
    <div class="container-shop space-y-12 py-10">
        <header class="space-y-3">
            <p class="type-caption text-[var(--pink-700)]">Dev only · /styleguide</p>
            <h1 class="type-display">Sweet Boutique</h1>
            <p class="max-w-2xl text-[var(--cocoa-700)]">مرجع المكوّنات والحالات البصرية لإيما سويت.</p>
        </header>

        <section class="space-y-4">
            <h2 class="section-title mb-0">الألوان</h2>
            <span class="section-underline" />
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-6">
                <div v-for="c in [
                    ['pink-600', 'var(--pink-600)'], ['pink-100', 'var(--pink-100)'],
                    ['denim-500', 'var(--denim-500)'], ['cocoa-900', 'var(--cocoa-900)'],
                    ['cream', 'var(--cream)'], ['sun', 'var(--sun)'],
                    ['mint', 'var(--mint)'], ['sky', 'var(--sky)'], ['lilac', 'var(--lilac)'], ['coral', 'var(--coral)'],
                ]" :key="c[0]" class="overflow-hidden rounded-[var(--r-md)] shadow-[var(--sh-sm)]">
                    <div class="h-16" :style="{ background: c[1] }" />
                    <div class="bg-[var(--surface)] px-2 py-2 text-[12px] font-bold">{{ c[0] }}</div>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="section-title mb-0">الخطوط</h2>
            <span class="section-underline" />
            <p class="type-display">Display · Baloo Bhaijaan 2</p>
            <p class="type-h1">H1 عنوان رئيسي</p>
            <p class="section-title">H2 عنوان قسم</p>
            <p class="type-h3">H3 عنوان كارت</p>
            <p class="text-base leading-[1.75]">Body — النص العادي بخط Cairo، سطر مريح للقراءة العربية.</p>
            <p class="type-price">٢٤٠ ج.م</p>
        </section>

        <section class="space-y-4">
            <h2 class="section-title mb-0">الأزرار</h2>
            <span class="section-underline" />
            <div class="flex flex-wrap gap-3">
                <button class="btn-primary">أساسي</button>
                <button class="btn-primary" disabled>معطّل</button>
                <button class="btn-secondary">ثانوي</button>
                <button class="btn-ghost" aria-label="أيقونة">♡</button>
                <button class="btn-icon-add" aria-label="إضافة">+</button>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="section-title mb-0">Chips · Segmented · Badges</h2>
            <span class="section-underline" />
            <div class="flex flex-wrap gap-2">
                <button class="chip">فلتر</button>
                <button class="chip chip-active">✓ نشط</button>
            </div>
            <div class="segmented">
                <button class="segmented-item is-active">متاح دلوقتي</button>
                <button class="segmented-item">متاح بكرة</button>
                <button class="segmented-item">بالحجز</button>
            </div>
            <div class="flex flex-wrap gap-2">
                <StatusBadge type="available_now" label="متاح دلوقتي" />
                <StatusBadge type="available_tomorrow" label="متاح بكرة" />
                <StatusBadge type="pre_order" label="بالحجز" />
                <StatusBadge type="sold_out" label="خلص" />
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="status-pill status-awaiting_payment">في انتظار الدفع</span>
                <span class="status-pill status-preparing">بيتجّهز</span>
                <span class="status-pill status-delivered">اتسلّم</span>
                <span class="status-pill status-cancelled">ملغي</span>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="section-title mb-0">حقول · Stepper · Alert</h2>
            <span class="section-underline" />
            <div class="field max-w-md">
                <label class="field-label">اسم المستلم <span class="field-required">*</span></label>
                <div class="field-control"><input placeholder="اكتبي الاسم" /></div>
            </div>
            <div class="field field--error max-w-md">
                <label class="field-label">موبايل</label>
                <div class="field-control"><input value="01" /></div>
                <p class="field-error">رقّمي الموبايل مش كامل</p>
            </div>
            <div class="qty-stepper"><button>−</button><span>2</span><button>+</button></div>
            <div class="alert alert--warning max-w-lg">التوصيل هيكون بكرة بسبب وقت التحضير.</div>
        </section>

        <section class="space-y-4">
            <h2 class="section-title mb-0">زخارف</h2>
            <span class="section-underline" />
            <div class="flex items-center gap-3">
                <Sparkle :size="12" />
                <Sparkle :size="16" />
                <Sparkle :size="24" tone="sun" />
            </div>
            <div class="hero-blob !h-40 !w-40">
                <img src="/images/mascots.svg" class="h-28 w-28" alt="" />
            </div>
            <div class="donut-loader" />
            <div class="skeleton h-20 w-full max-w-sm" />
        </section>

        <WaveDivider />
    </div>
</template>
