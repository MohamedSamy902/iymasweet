<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });

const props = defineProps({
    howSteps: { type: Array, default: () => [] },
});

const page = usePage();
const { t } = useUiText();
const brand = computed(() => page.props.settings?.brand || {});

const statusTabs = computed(() => [
    { key: 'available_now', label: t('home.status_available_now', 'متاح دلوقتي'), tone: 'bg-[var(--color-mint)]/40 text-[var(--color-brown)]' },
    { key: 'tomorrow', label: t('home.status_tomorrow', 'متاح بكرة'), tone: 'bg-[var(--color-sky)]/40 text-[var(--color-denim)]' },
    { key: 'preorder', label: t('home.status_preorder', 'بالحجز'), tone: 'bg-[var(--color-lilac)]/40 text-[var(--color-brown)]' },
    { key: 'sold_out', label: t('home.status_sold_out', 'خلص (وهيرجع قريب)'), tone: 'bg-[var(--color-primary-soft)] text-[var(--color-text-muted)]' },
]);
</script>

<template>
    <Head :title="brand.name || 'Iyma Sweet'" />

    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <span class="sparkle absolute start-[8%] top-16 text-2xl animate-float">✦</span>
            <span class="sparkle absolute end-[12%] top-28 text-xl animate-float" style="animation-delay: .6s">✦</span>
            <span class="sparkle absolute start-[40%] top-10 text-lg animate-float" style="animation-delay: 1.1s">✦</span>
        </div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 pb-14 pt-10 md:grid-cols-2 md:items-center md:pt-16">
            <div class="animate-fade-up space-y-5">
                <p class="text-sm font-semibold text-[var(--color-primary-dark)]">
                    {{ t('home.welcome', 'أهلاً بيكي يا قمر 🍩') }}
                </p>
                <h1 class="font-heading text-4xl leading-tight text-[var(--color-brown)] md:text-5xl">
                    {{ brand.name || 'Iyma Sweet' }}
                </h1>
                <p class="max-w-md text-base text-[var(--color-text-muted)] md:text-lg">
                    {{ t('home.hero_subtitle', 'دونتس، تورتات، وبوكسات هدايا بطعم يفرّحك أنتِ واللي بتحبيهم') }}
                </p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <Link href="/shop" class="btn-primary">
                        {{ t('home.cta_order_now', 'اطلب دلوقتي') }}
                    </Link>
                    <Link href="/shop" class="btn-secondary">
                        {{ t('home.cta_see_menu', 'شوف المنيو') }}
                    </Link>
                </div>
            </div>

            <div class="relative animate-fade-up" style="animation-delay: .12s">
                <div
                    class="aspect-[4/3] overflow-hidden rounded-[2rem] bg-gradient-to-br from-[var(--color-primary-soft)] via-white to-[var(--color-denim-soft)] p-6 shadow-[var(--shadow-soft)]"
                >
                    <div class="flex h-full flex-col items-center justify-center text-center">
                        <div class="mb-4 text-7xl animate-float">🍩</div>
                        <div class="font-heading text-3xl text-[var(--color-primary-dark)]">
                            {{ brand.name || 'Iyma Sweet' }}
                        </div>
                        <div class="mt-2 text-sm text-[var(--color-text-muted)]">
                            {{ brand.tagline || 'Handmade with love' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-4">
        <div class="rainbow-divider mb-10" />

        <section class="animate-fade-up space-y-4">
            <h2 class="font-heading text-2xl text-[var(--color-brown)]">
                {{ t('home.status_available_now', 'متاح دلوقتي') }}
            </h2>
            <div class="flex gap-2 overflow-x-auto pb-2">
                <button
                    v-for="tab in statusTabs"
                    :key="tab.key"
                    type="button"
                    class="chip whitespace-nowrap"
                    :class="tab.tone"
                >
                    {{ tab.label }}
                </button>
            </div>
            <div class="rounded-3xl border border-dashed border-[var(--color-primary)]/40 bg-white/60 p-8 text-center text-[var(--color-text-muted)]">
                {{ t('common.loading', 'ثواني…') }} — {{ t('shop.empty_hint', 'المنتجات هتتضاف في المرحلة الجاية') }}
            </div>
        </section>

        <section class="mt-14 space-y-5">
            <h2 class="font-heading text-2xl text-[var(--color-brown)]">
                {{ t('home.how_title', 'إزاي تطلب؟') }}
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="(step, index) in howSteps"
                    :key="index"
                    class="rounded-3xl bg-white/80 p-5 shadow-sm transition duration-200 hover:-translate-y-1"
                >
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-[var(--color-primary-soft)] font-heading text-[var(--color-primary-dark)]">
                        {{ index + 1 }}
                    </div>
                    <div class="font-semibold text-[var(--color-text)]">
                        {{ t(step.title_key, step.fallback) }}
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-16 mb-8 overflow-hidden rounded-[2rem] bg-gradient-to-l from-[var(--color-primary)] to-[var(--color-primary-dark)] p-8 text-white md:p-12">
            <h2 class="font-heading text-3xl">
                {{ t('home.cta_final_title', 'جعانين لحاجة حلوة؟') }}
            </h2>
            <p class="mt-2 max-w-lg text-white/90">
                {{ t('home.hero_subtitle', 'يلا نبدأ طلبك دلوقتي') }}
            </p>
            <Link href="/shop" class="mt-6 inline-flex rounded-full bg-white px-6 py-3 text-sm font-bold text-[var(--color-primary-dark)]">
                {{ t('home.cta_final_button', 'يلا نبدأ الطلب') }}
            </Link>
        </section>
    </div>
</template>
