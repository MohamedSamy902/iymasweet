<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({ products: Object, filters: Object, categories: Array, attributes: Array, category: Object });
const { t } = useUiText();

const updateFilter = (key, value) => {
    router.get(window.location.pathname, { ...props.filters, [key]: value || undefined }, { preserveState: true, replace: true });
};
</script>

<template>
    <Head :title="category?.name_ar || t('shop.title')" />
    <div class="mx-auto max-w-6xl px-4 py-6">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ category?.name_ar || t('shop.title') }}</h1>

        <div class="mt-4 h-scroll pb-2">
            <button class="chip" :class="{ 'chip-active': !filters.availability }" @click="updateFilter('availability', null)">الكل</button>
            <button class="chip" :class="{ 'chip-active': filters.availability==='available_now' }" @click="updateFilter('availability','available_now')">{{ t('home.status_available_now') }}</button>
            <button class="chip" :class="{ 'chip-active': filters.availability==='available_tomorrow' }" @click="updateFilter('availability','available_tomorrow')">{{ t('home.status_tomorrow') }}</button>
            <button class="chip" :class="{ 'chip-active': filters.availability==='pre_order' }" @click="updateFilter('availability','pre_order')">{{ t('home.status_preorder') }}</button>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <input class="rounded-full border px-4 py-2 text-sm" :placeholder="t('layout.nav_search')" :value="filters.q" @change="updateFilter('q', $event.target.value)" />
            <select class="rounded-full border px-4 py-2 text-sm" :value="filters.sort || 'available_first'" @change="updateFilter('sort', $event.target.value)">
                <option value="available_first">المتاح الأول</option>
                <option value="latest">{{ t('shop.sort_latest') }}</option>
                <option value="bestseller">{{ t('shop.sort_bestseller') }}</option>
                <option value="price_asc">{{ t('shop.sort_price_asc') }}</option>
                <option value="price_desc">{{ t('shop.sort_price_desc') }}</option>
                <option value="rating">{{ t('shop.sort_rating') }}</option>
            </select>
        </div>

        <div class="mt-4 h-scroll">
            <Link v-for="c in categories" :key="c.id" :href="`/category/${c.slug}`" class="chip whitespace-nowrap">{{ c.name_ar }}</Link>
        </div>

        <div v-if="products.data?.length" class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
            <ProductCard v-for="p in products.data" :key="p.id" :product="p" />
        </div>
        <div v-else class="mt-10 rounded-3xl bg-white p-10 text-center text-[var(--color-text-muted)]">
            <div class="font-heading text-xl text-[var(--color-brown)]">{{ t('shop.empty_title') }}</div>
            <p class="mt-2">{{ t('shop.empty_hint') }}</p>
        </div>

        <div v-if="products.next_page_url" class="mt-8 text-center">
            <Link :href="products.next_page_url" class="btn-secondary">{{ t('shop.load_more') }}</Link>
        </div>
    </div>
</template>
