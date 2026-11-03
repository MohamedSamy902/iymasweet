<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
defineOptions({ layout: ShopLayout });
defineProps({ bundles: Array });
</script>
<template>
  <Head title="الباكدجات" />
  <div class="mx-auto max-w-6xl px-4 py-8 grid gap-4 md:grid-cols-3">
    <Link v-for="b in bundles" :key="b.slug" :href="`/bundle/${b.slug}`" class="rounded-3xl bg-white p-4 shadow-sm">
      <img :src="b.image || '/images/logo-placeholder.svg'" class="aspect-video w-full rounded-2xl object-cover" />
      <div class="mt-3 font-heading text-xl text-[var(--color-brown)]">{{ b.name_ar }}</div>
      <div class="font-bold text-[var(--color-primary-dark)]">{{ b.price }} ج.م</div>
    </Link>
  </div>
</template>
