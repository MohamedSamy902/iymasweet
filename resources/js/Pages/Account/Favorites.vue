<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
defineOptions({ layout: ShopLayout });
defineProps({ favorites: Array });
</script>
<template>
  <Head title="المفضلة" />
  <div class="mx-auto max-w-2xl px-4 py-8 grid grid-cols-2 gap-3">
    <Link v-for="f in favorites" :key="f.id" :href="`/product/${f.product.slug}`" class="rounded-3xl bg-white p-3 shadow-sm">
      <img :src="f.product.image || '/images/logo-placeholder.svg'" class="aspect-square w-full rounded-2xl object-cover" />
      <div class="mt-2 font-semibold">{{ f.product.name_ar }}</div>
    </Link>
  </div>
</template>
