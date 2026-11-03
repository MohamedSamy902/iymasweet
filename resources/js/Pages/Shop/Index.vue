<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { SlidersHorizontal } from '@lucide/vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({ products: Object, filters: Object, categories: Array, attributes: Array, category: Object });
const { t } = useUiText();

const total = computed(() => props.products?.total ?? props.products?.data?.length ?? 0);

const updateFilter = (key, value) => {
    router.get(window.location.pathname, { ...props.filters, [key]: value || undefined }, { preserveState: true, replace: true });
};

const clearFilters = () => {
    router.get(window.location.pathname, {}, { preserveState: true, replace: true });
};
</script>

<template>
    <Head :title="category?.name_ar || t('shop.title', 'المنيو')" />
    <div class="container-shop py-6 pb-16">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="type-h1">{{ category?.name_ar || t('shop.title', 'المنيو') }}</h1>
                <p class="mt-1 text-sm text-[var(--cocoa-500)]">
                    <span class="dir-ltr font-semibold text-[var(--cocoa-900)]">{{ total }}</span>
                    {{ t('shop.results', 'نتيجة') }}
                </p>
            </div>
        </div>

        <div class="sticky top-[60px] z-30 -mx-[var(--gutter)] mt-4 border-b border-[var(--cocoa-100)] bg-[var(--cream)]/95 px-[var(--gutter)] py-3 backdrop-blur-md lg:top-[76px]">
            <div class="flex gap-2">
                <input
                    class="min-h-11 flex-1 rounded-full border border-[var(--cocoa-100)] bg-[var(--surface)] px-4 text-sm outline-none focus:border-[var(--pink-500)] focus:shadow-[var(--ring)]"
                    :placeholder="t('layout.nav_search', 'دور على حاجة حلوة…')"
                    :value="filters.q"
                    @change="updateFilter('q', $event.target.value)"
                />
                <button type="button" class="inline-flex h-11 items-center gap-2 rounded-full border border-[var(--cocoa-100)] bg-[var(--surface)] px-4 text-sm font-bold text-[var(--cocoa-700)]">
                    <SlidersHorizontal class="h-4 w-4" :stroke-width="1.75" />
                    {{ t('shop.filter', 'فلتر') }}
                </button>
                <select
                    class="h-11 rounded-full border border-[var(--cocoa-100)] bg-[var(--surface)] px-3 text-sm font-semibold text-[var(--cocoa-700)]"
                    :value="filters.sort || 'available_first'"
                    @change="updateFilter('sort', $event.target.value)"
                >
                    <option value="available_first">{{ t('shop.sort_available', 'المتاح الأول') }}</option>
                    <option value="latest">{{ t('shop.sort_latest') }}</option>
                    <option value="bestseller">{{ t('shop.sort_bestseller') }}</option>
                    <option value="price_asc">{{ t('shop.sort_price_asc') }}</option>
                    <option value="price_desc">{{ t('shop.sort_price_desc') }}</option>
                    <option value="rating">{{ t('shop.sort_rating') }}</option>
                </select>
            </div>
        </div>

        <div class="mt-4 h-scroll gap-2">
            <button class="chip" :class="{ 'chip-active': !filters.availability }" @click="updateFilter('availability', null)">{{ t('common.all', 'الكل') }}</button>
            <button class="chip" :class="{ 'chip-active': filters.availability==='available_now' }" @click="updateFilter('availability','available_now')">{{ t('home.status_available_now') }}</button>
            <button class="chip" :class="{ 'chip-active': filters.availability==='available_tomorrow' }" @click="updateFilter('availability','available_tomorrow')">{{ t('home.status_tomorrow') }}</button>
            <button class="chip" :class="{ 'chip-active': filters.availability==='pre_order' }" @click="updateFilter('availability','pre_order')">{{ t('home.status_preorder') }}</button>
        </div>

        <div class="mt-3 h-scroll gap-2">
            <Link
                v-for="c in categories"
                :key="c.id"
                :href="`/category/${c.slug}`"
                class="chip whitespace-nowrap"
                :class="{ 'chip-active': category?.id === c.id }"
            >
                {{ c.name_ar }}
            </Link>
        </div>

        <div v-if="products.data?.length" class="product-grid mt-6">
            <ProductCard v-for="p in products.data" :key="p.id" :product="p" />
        </div>

        <div v-else class="mt-12 rounded-[var(--r-xl)] bg-[var(--surface)] px-6 py-12 text-center shadow-[var(--sh-sm)]">
            <img src="/images/mascots.svg" alt="" class="mx-auto h-36 w-36 object-contain" />
            <div class="mt-4 font-heading text-xl font-extrabold text-[var(--cocoa-900)]">{{ t('shop.empty_title', 'ملقيناش اللي بتدوّر عليه') }}</div>
            <p class="mt-2 text-sm text-[var(--cocoa-500)]">{{ t('shop.empty_hint', 'جرّب تمسح الفلاتر أو اسألنا على واتساب.') }}</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <button type="button" class="btn-secondary" @click="clearFilters">{{ t('shop.clear_filters', 'امسح الفلاتر') }}</button>
                <Link href="/shop" class="btn-primary">{{ t('home.cta_see_menu', 'شوف المنيو') }}</Link>
            </div>
        </div>

        <div v-if="products.next_page_url" class="mt-10 text-center">
            <Link :href="products.next_page_url" class="btn-secondary">{{ t('shop.load_more', 'حمّل المزيد') }}</Link>
        </div>
    </div>
</template>
