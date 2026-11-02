<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';
defineOptions({ layout: ShopLayout });
defineProps({ orders: Object });
const { t } = useUiText();
</script>
<template>
  <Head :title="t('account.orders')" />
  <div class="mx-auto max-w-2xl px-4 py-8 space-y-3">
    <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('account.orders') }}</h1>
    <Link v-for="o in orders.data" :key="o.number" :href="`/account/orders/${o.number}`" class="block rounded-3xl bg-white p-4 shadow-sm">
      <div class="flex justify-between"><strong>{{ o.number }}</strong><span>{{ o.status_label }}</span></div>
      <div class="text-sm text-[var(--color-text-muted)]">{{ o.total }} ج.م · {{ o.delivery_date }}</div>
    </Link>
  </div>
</template>
