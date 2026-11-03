<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Bike, HeartHandshake, Wallet } from '@lucide/vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import Sparkle from '@/Components/Ui/Sparkle.vue';
import WaveDivider from '@/Components/Ui/WaveDivider.vue';
import { useUiText } from '@/Composables/useUiText';

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

const tabCount = (key) => (props.statusProducts?.[key] || []).length;

const categoryTint = (c, i) => {
    if (c.color) return c.color + '55';
    const tones = ['var(--pink-100)', 'var(--denim-100)', '#FFF0D6', '#EFE9FB', 'var(--pink-50)', '#E2F6EC'];
    return tones[i % tones.length];
};

const accentForStep = (i) => {
    const accents = ['var(--coral)', 'var(--sun)', 'var(--mint)', 'var(--sky)'];
    return accents[i % accents.length];
};

const copyPromo = async () => {
    try {
        await navigator.clipboard?.writeText('IYMA10');
    } catch (_) {}
};
</script>

<template>
    <Head :title="brand.name || 'Iyma Sweet'" />

    <!-- Hero -->
    <section
        class="relative overflow-hidden"
        style="background: linear-gradient(160deg, var(--pink-100), var(--cream) 55%, var(--denim-50))"
    >
        <div class="pointer-events-none absolute inset-0 sprinkle-bg opacity-[0.12]" aria-hidden="true" />
        <div class="container-shop relative grid items-center gap-8 pb-16 pt-6 lg:min-h-[640px] lg:grid-cols-[55%_45%] lg:gap-10 lg:pb-20 lg:pt-10">
            <div class="animate-fade-up space-y-5 text-center lg:text-start">
                <div
                    class="mx-auto inline-flex h-8 items-center gap-2 rounded-full bg-[var(--surface)]/90 px-3 text-[12px] font-bold text-[var(--pink-700)] shadow-[var(--sh-sm)] lg:mx-0"
                >
                    <Sparkle :size="12" />
                    {{ brand.tagline || 'Handmade with love' }}
                </div>

                <h1 class="type-display mx-auto max-w-[14ch] lg:mx-0">
                    <span class="block">{{ t('home.hero_kicker', 'حلويات بوتيك معمولة بحب من') }}</span>
                    <span class="relative mt-1 inline-block text-[var(--pink-600)]">
                        {{ brand.name || t('home.hero_title', 'Iyma Sweet') }}
                        <span class="section-underline absolute inset-x-0 -bottom-1 mx-auto lg:mx-0" />
                    </span>
                </h1>

                <p class="mx-auto max-w-md text-base leading-relaxed text-[var(--cocoa-700)] line-clamp-2 lg:mx-0 lg:text-[17px]">
                    {{ t('home.hero_subtitle', 'دونتس، تورت، كوكيز وبوكسات هدايا — طازج، لطيف، وبيوصلك لحد البيت.') }}
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3 pt-1 lg:justify-start">
                    <Link href="/shop" class="btn-primary">{{ t('home.cta_order_now', 'اطلب دلوقتي') }}</Link>
                    <Link href="/shop" class="btn-secondary">{{ t('home.cta_see_menu', 'شوف المنيو') }}</Link>
                </div>
            </div>

            <div class="relative mx-auto">
                <div class="hero-blob animate-float">
                    <img
                        src="/images/mascots.svg"
                        alt=""
                        class="relative z-[1] h-[78%] w-[78%] object-contain"
                        width="360"
                        height="320"
                        fetchpriority="high"
                    />
                    <span class="sparkle absolute start-[8%] top-[12%]" style="animation-delay: 0s"><Sparkle :size="24" /></span>
                    <span class="sparkle sparkle--sun absolute end-[10%] top-[18%]" style="animation-delay: 1.2s"><Sparkle :size="16" tone="sun" /></span>
                    <span class="sparkle absolute start-[14%] bottom-[22%]" style="animation-delay: 2.4s"><Sparkle :size="12" /></span>
                    <span class="sparkle sparkle--sun absolute end-[16%] bottom-[16%]" style="animation-delay: 3.1s"><Sparkle :size="20" tone="sun" /></span>
                    <span class="sparkle absolute start-[46%] top-[4%]" style="animation-delay: 0.8s"><Sparkle :size="14" /></span>
                </div>
            </div>

            <div class="col-span-full grid grid-cols-3 gap-2 border-t border-[var(--pink-200)]/60 pt-6 text-center lg:gap-6">
                <div class="space-y-1.5">
                    <Bike class="mx-auto h-6 w-6 text-[var(--pink-600)]" :stroke-width="1.75" />
                    <div class="text-[12px] font-bold text-[var(--cocoa-900)] lg:text-[14px]">{{ t('home.trust_delivery', 'توصيل لحد البيت') }}</div>
                </div>
                <div class="space-y-1.5">
                    <HeartHandshake class="mx-auto h-6 w-6 text-[var(--pink-600)]" :stroke-width="1.75" />
                    <div class="text-[12px] font-bold text-[var(--cocoa-900)] lg:text-[14px]">{{ t('home.trust_handmade', 'متعمل بحب') }}</div>
                </div>
                <div class="space-y-1.5">
                    <Wallet class="mx-auto h-6 w-6 text-[var(--pink-600)]" :stroke-width="1.75" />
                    <div class="text-[12px] font-bold text-[var(--cocoa-900)] lg:text-[14px]">{{ t('home.trust_pay', 'دفع سهل') }}</div>
                </div>
            </div>
        </div>
        <WaveDivider color="var(--cream)" />
    </section>

    <div class="container-shop relative z-[1] -mt-7 space-y-14 pb-16 lg:space-y-24 lg:pb-24">
        <!-- Status heart of store -->
        <section class="surface-card space-y-4 p-4 shadow-[var(--sh-md)] lg:p-6">
            <div class="segmented w-full">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    class="segmented-item"
                    :class="{ 'is-active': activeTab === tab.key }"
                    @click="activeTab = tab.key"
                >
                    {{ tab.label }}
                    <span class="ms-1 rounded-full bg-[var(--pink-50)] px-1.5 text-[11px] font-bold text-[var(--pink-700)]">
                        {{ tabCount(tab.key) }}
                    </span>
                </button>
            </div>
            <div class="h-scroll gap-3">
                <div v-for="p in (statusProducts?.[activeTab] || []).slice(0, 8)" :key="p.id" class="w-[168px]">
                    <ProductCard :product="p" />
                </div>
            </div>
            <div class="text-end">
                <Link href="/shop" class="text-sm font-bold text-[var(--pink-700)]">{{ t('common.see_all', 'شوف الكل') }}</Link>
            </div>
        </section>

        <!-- Categories -->
        <section>
            <h2 class="section-title mb-0">{{ t('home.categories_title', 'التصنيفات') }}</h2>
            <span class="section-underline mb-5" />
            <div class="h-scroll gap-4 lg:grid lg:grid-cols-4 lg:gap-6 lg:overflow-visible">
                <Link
                    v-for="(c, i) in categories"
                    :key="c.id"
                    :href="`/category/${c.slug}`"
                    class="category-pill lg:flex lg:h-24 lg:w-auto lg:flex-row lg:items-center lg:gap-4 lg:rounded-[var(--r-lg)] lg:bg-[var(--surface)] lg:p-4 lg:shadow-[var(--sh-sm)]"
                >
                    <div
                        class="category-pill__icon lg:!h-16 lg:!w-16 lg:!rounded-[20px]"
                        :style="{ background: categoryTint(c, i) }"
                    >
                        <span class="font-heading text-xl text-[var(--pink-600)]">{{ (c.name_ar || '?').slice(0, 1) }}</span>
                    </div>
                    <span class="category-pill__label lg:text-start lg:text-[15px]">{{ c.name_ar }}</span>
                </Link>
            </div>
        </section>

        <!-- Featured -->
        <section>
            <div class="mb-5 flex items-end justify-between gap-3">
                <div>
                    <h2 class="section-title mb-0">{{ t('home.featured_title', 'الأكتر طلباً') }}</h2>
                    <span class="section-underline" />
                </div>
                <Link href="/shop" class="shrink-0 text-sm font-bold text-[var(--denim-700)]">{{ t('common.see_all', 'شوف الكل') }}</Link>
            </div>
            <div class="product-grid">
                <ProductCard v-for="p in (featured || []).slice(0, 4)" :key="p.id" :product="p" />
            </div>
        </section>
    </div>

    <!-- Bundles on tinted scallop -->
    <section class="scallop-top">
        <div class="container-shop space-y-5 pb-16 pt-4 lg:pb-24">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <h2 class="section-title mb-0">{{ t('home.bundles_title', 'الباكدجات والبوكسات') }}</h2>
                    <span class="section-underline" />
                </div>
                <Link href="/bundles" class="text-sm font-bold text-[var(--denim-700)]">{{ t('common.see_all', 'شوف الكل') }}</Link>
            </div>
            <div class="h-scroll gap-4">
                <Link
                    v-for="b in bundles"
                    :key="b.slug"
                    :href="`/bundle/${b.slug}`"
                    class="w-[280px] overflow-hidden rounded-[var(--r-lg)] bg-[var(--surface)] p-3 shadow-[var(--sh-sm)] transition hover:-translate-y-0.5 hover:shadow-[var(--sh-md)]"
                >
                    <div class="relative overflow-hidden rounded-[18px]">
                        <img :src="b.image || '/images/products/box-1.jpg'" class="aspect-[16/10] w-full object-cover" alt="" loading="lazy" />
                        <span
                            v-if="b.compare_at_price && b.compare_at_price > b.price"
                            class="absolute start-2 top-2 rounded-full bg-[var(--sun)] px-2.5 py-1 text-[11px] font-bold text-[var(--cocoa-900)]"
                        >
                            وفّر {{ Math.round(b.compare_at_price - b.price) }} ج.م
                        </span>
                    </div>
                    <div class="space-y-2 p-2 pt-3">
                        <div class="type-h3">{{ b.name_ar }}</div>
                        <div class="type-price dir-ltr">{{ b.price }} <span class="text-[13px]">ج.م</span></div>
                        <span class="btn-secondary !min-h-10 w-full !px-4 !text-sm">{{ t('home.choose_box', 'اختار البوكس') }}</span>
                    </div>
                </Link>
            </div>
        </div>
    </section>

    <WaveDivider color="var(--cream)" />

    <div class="container-shop space-y-14 py-14 lg:space-y-24 lg:py-20">
        <!-- How to order -->
        <section>
            <h2 class="section-title mb-0">{{ t('home.how_title', 'إزاي تطلب؟') }}</h2>
            <span class="section-underline mb-6" />
            <div class="grid gap-5 lg:grid-cols-4">
                <div
                    v-for="(step, index) in howSteps"
                    :key="index"
                    class="relative flex gap-4 lg:flex-col lg:text-center"
                >
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full font-heading text-lg font-extrabold text-[var(--cocoa-900)] lg:mx-auto"
                        :style="{ background: accentForStep(index) }"
                    >
                        {{ index + 1 }}
                    </div>
                    <div class="font-semibold leading-relaxed text-[var(--cocoa-900)]">
                        {{ t(step.title_key, step.fallback) }}
                    </div>
                </div>
            </div>
        </section>

        <!-- Reviews -->
        <section v-if="reviews?.length" class="-mx-[var(--gutter)] bg-[var(--surface-tint)] px-[var(--gutter)] py-12 lg:mx-0 lg:rounded-[var(--r-xl)] lg:px-8">
            <h2 class="section-title mb-0">{{ t('home.reviews_title', 'آراء العملاء') }}</h2>
            <span class="section-underline mb-6" />
            <div class="h-scroll gap-4">
                <article
                    v-for="r in reviews"
                    :key="r.id"
                    class="w-[280px] rounded-[var(--r-lg)] bg-[var(--surface)] p-5 shadow-[var(--sh-sm)]"
                >
                    <div class="mb-2 flex gap-0.5 text-[var(--sun)]">
                        <span v-for="n in 5" :key="n">★</span>
                    </div>
                    <p class="line-clamp-3 text-[15px] leading-relaxed text-[var(--cocoa-700)]">{{ r.comment || r.body_ar }}</p>
                    <div class="mt-4 flex items-center justify-between gap-2">
                        <span class="text-sm font-bold">{{ r.user?.name || r.name_ar || 'عميل' }}</span>
                        <span class="rounded-full bg-[var(--pink-50)] px-2 py-1 text-[11px] font-bold text-[var(--pink-700)]">
                            {{ t('home.verified_buyer', 'عميل موثّق') }}
                        </span>
                    </div>
                </article>
            </div>
        </section>

        <!-- Promo banner -->
        <section
            class="relative overflow-hidden rounded-[var(--r-xl)] p-8 text-white md:p-12"
            style="background: linear-gradient(135deg, var(--pink-500), var(--pink-400))"
        >
            <div class="pointer-events-none absolute inset-0 sprinkle-bg opacity-20" aria-hidden="true" />
            <div class="relative space-y-4">
                <h2 class="font-heading text-2xl font-extrabold md:text-3xl">{{ t('home.promo_title', 'عرض خاص لأول طلب') }}</h2>
                <p class="max-w-md text-sm text-white/90">{{ t('home.promo_subtitle', 'استخدم الكود وخد خصم على أول طلبية حلويات.') }}</p>
                <div class="inline-flex items-center gap-2 rounded-full border border-dashed border-white/70 bg-white/15 px-4 py-2 font-bold tracking-wide">
                    <span class="dir-ltr">IYMA10</span>
                    <button type="button" class="rounded-full bg-white px-3 py-1 text-xs font-bold text-[var(--pink-700)]" @click="copyPromo">
                        {{ t('common.copy', 'نسخ') }}
                    </button>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section v-if="faqs?.length">
            <h2 class="section-title mb-0">{{ t('home.faq_title', 'أسئلة شائعة') }}</h2>
            <span class="section-underline mb-5" />
            <div class="divide-y divide-[var(--cocoa-100)] overflow-hidden rounded-[var(--r-lg)] bg-[var(--surface)] shadow-[var(--sh-sm)]">
                <details v-for="f in faqs.slice(0, 4)" :key="f.id" class="group px-5 py-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-[16px] font-bold">
                        {{ f.question_ar }}
                        <span class="text-[var(--pink-500)] transition group-open:rotate-180">▾</span>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-[var(--cocoa-500)]">{{ f.answer_ar }}</p>
                </details>
            </div>
            <Link href="/faq" class="mt-4 inline-block text-sm font-bold text-[var(--denim-700)]">{{ t('common.see_all', 'شوف الكل') }}</Link>
        </section>

        <!-- Final CTA -->
        <section class="grid items-center gap-6 overflow-hidden rounded-[var(--r-xl)] bg-[var(--pink-50)] p-6 md:grid-cols-[1fr_auto] md:p-10">
            <div class="space-y-4">
                <h2 class="type-h1">{{ t('home.cta_final_title', 'جاهز تملأ البيت حلاوة؟') }}</h2>
                <p class="max-w-md text-[var(--cocoa-700)]">{{ t('home.cta_final_subtitle', 'اطلب دلوقتي، أو فعّل الإشعارات عشان تعرف أول ما ينزل الجديد.') }}</p>
                <Link href="/shop" class="btn-primary">{{ t('home.cta_final_button', 'اطلب دلوقتي') }}</Link>
            </div>
            <img src="/images/mascots.svg" alt="" class="mx-auto h-40 w-40 object-contain md:h-48 md:w-48" loading="lazy" />
        </section>
    </div>
</template>
