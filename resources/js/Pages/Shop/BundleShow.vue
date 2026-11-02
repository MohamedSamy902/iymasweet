<script setup>
import { Head, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
defineOptions({ layout: ShopLayout });
const props = defineProps({ bundle: Object });
const add = () => router.post('/cart', { bundle_id: null, product_id: null });
// Use slug lookup via form - need id. We'll post using a workaround: create endpoint uses bundle - fix by including id
</script>
<template>
  <Head :title="bundle.name_ar" />
  <div class="mx-auto max-w-3xl px-4 py-8 space-y-4">
    <img :src="bundle.image || '/images/logo-placeholder.svg'" class="aspect-video w-full rounded-[2rem] object-cover" />
    <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ bundle.name_ar }}</h1>
    <p>{{ bundle.description_ar }}</p>
    <div class="text-2xl font-bold text-[var(--color-primary-dark)]">{{ bundle.price }} ج.م</div>
    <button class="btn-primary" @click="router.post('/cart', { bundle_id: bundle.id || undefined })">أضف للسلة</button>
  </div>
</template>
