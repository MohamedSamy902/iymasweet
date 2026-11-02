<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import { useUiText } from '@/Composables/useUiText';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: ShopLayout });

const props = defineProps({
    sections: Array,
    categories: Array,
    statusProducts: Object,
    featured: Array,
    bundles: Array,
    reviews: Array,
    faqs: Array,
    howSteps: Array,
});

const page = usePage();
const { t } = useUiText();
const brand = computed(() => page.props.settings?.brand || {});
const activeTab = ref('available_now');

const tabs = computed(() => [
    { key: 'available_now', label: t('home.status_available_now', 'متاح دلوقتي') },
    { key: 'available_tomorrow', label: t('home.status_tomorrow', 'متاح بكرة') },
    { key: 'pre_order', label: t('home.status_preorder', 'بالحجز') },
    { key: 'sold_out', label: t('home.status_sold_out', 'خلص') },
]);
</script>

<template>
    <Head :title="brand.name || 'Iyma Sweet'" />

    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <span class="sparkle absolute start-[8%] top-16 text-2xl animate-float">✦</span>
            <span class="sparkle absolute end-[12%] top-28 text-xl animate-float" style="animation-delay:.6s">✦</span>
        </div>
        <div class="mx-auto grid max-w-6xl gap-10 px-4 pb-14 pt-10 md:grid-cols-2 md:items-center">
            <div class="animate-fade-up space-y-5">
                <p class="text-sm font-semibold text-[var(--color-primary-dark)]">{{ t('home.welcome') }}</p>
                <h1 class="font-heading text-4xl leading-tight text-[var(--color-brown)] md:text-5xl">{{ brand.name || 'Iyma Sweet' }}</h1>
                <p class="max-w-md text-base text-[var(--color-text-muted)] md:text-lg">{{ t('home.hero_subtitle') }}</p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <Link href="/shop" class="btn-primary">{{ t('home.cta_order_now') }}</Link>
                    <Link href="/shop" class="btn-secondary">{{ t('home.cta_see_menu') }}</Link>
                </div>
            </div>
            <div class="relative animate-fade-up">
                <div class="aspect-[4/3] overflow-hidden rounded-[2rem] bg-gradient-to-br from-[var(--color-primary-soft)] via-white to-[var(--color-denim-soft)] p-6 shadow-[var(--shadow-soft)]">
                    <div class="flex h-full flex-col items-center justify-center text-center">
                        <div class="mb-4 text-7xl animate-float">🍩</div>
                        <div class="font-heading text-3xl text-[var(--color-primary-dark)]">{{ brand.name }}</div>
                        <div class="mt-2 text-sm text-[var(--color-text-muted)]">{{ brand.tagline }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl space-y-14 px-4 pb-10">
        <div class="rainbow-divider" />

        <section class="space-y-4">
            <div class="flex gap-2 overflow-x-auto pb-1">
                <button v-for="tab in tabs" :key="tab.key" type="button" class="chip whitespace-nowrap" :class="{ 'chip-active': activeTab===tab.key }" @click="activeTab=tab.key">{{ tab.label }}</button>
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <ProductCard v-for="p in (statusProducts?.[activeTab] || [])" :key="p.id" :product="p" />
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="font-heading text-2xl text-[var(--color-brown)]">{{ t('home.categories_title') }}</h2>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <Link v-for="c in categories" :key="c.id" :href="`/category/${c.slug}`" class="rounded-3xl p-5 text-center shadow-sm transition hover:-translate-y-1" :style="{ background: (c.color || '#FDE8EE') + '55' }">
                    <div class="font-heading text-lg text-[var(--color-brown)]">{{ c.name_ar }}</div>
                </Link>
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-2xl text-[var(--color-brown)]">{{ t('home.featured_title') }}</h2>
                <Link href="/shop" class="text-sm font-semibold text-[var(--color-denim)]">{{ t('common.see_all') }}</Link>
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <ProductCard v-for="p in featured" :key="p.id" :product="p" />
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-2xl text-[var(--color-brown)]">{{ t('home.bundles_title') }}</h2>
                <Link href="/bundles" class="text-sm font-semibold text-[var(--color-denim)]">{{ t('common.see_all') }}</Link>
            </div>
            <div class="grid gap-3 md:grid-cols-3">
                <Link v-for="b in bundles" :key="b.slug" :href="`/bundle/${b.slug}`" class="rounded-3xl bg-white p-4 shadow-sm">
                    <img :src="b.image || '/images/logo-placeholder.svg'" class="mb-3 aspect-video w-full rounded-2xl object-cover" />
                    <div class="font-semibold">{{ b.name_ar }}</div>
                    <div class="text-[var(--color-primary-dark)] font-bold">{{ b.price }} ج.م</div>
                </Link>
            </div>
        </section>

        <section class="space-y-5">
            <h2 class="font-heading text-2xl text-[var(--color-brown)]">{{ t('home.how_title') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="(step, index) in howSteps" :key="index" class="rounded-3xl bg-white/80 p-5 shadow-sm">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-[var(--color-primary-soft)] font-heading text-[var(--color-primary-dark)]">{{ index + 1 }}</div>
                    <div class="font-semibold">{{ t(step.title_key, step.fallback) }}</div>
                </div>
            </div>
        </section>

        <section v-if="faqs?.length" class="space-y-4">
            <h2 class="font-heading text-2xl text-[var(--color-brown)]">{{ t('home.faq_title') }}</h2>
            <details v-for="f in faqs" :key="f.id" class="rounded-2xl bg-white p-4 shadow-sm">
                <summary class="cursor-pointer font-semibold">{{ f.question_ar }}</summary>
                <p class="mt-2 text-sm text-[var(--color-text-muted)]">{{ f.answer_ar }}</p>
            </details>
            <Link href="/faq" class="text-sm font-semibold text-[var(--color-denim)]">{{ t('common.see_all') }}</Link>
        </section>

        <section class="overflow-hidden rounded-[2rem] bg-gradient-to-l from-[var(--color-primary)] to-[var(--color-primary-dark)] p-8 text-white md:p-12">
            <h2 class="font-heading text-3xl">{{ t('home.cta_final_title') }}</h2>
            <Link href="/shop" class="mt-6 inline-flex rounded-full bg-white px-6 py-3 text-sm font-bold text-[var(--color-primary-dark)]">{{ t('home.cta_final_button') }}</Link>
        </section>
    </div>
</template>
