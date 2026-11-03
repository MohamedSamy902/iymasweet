<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';
defineOptions({ layout: ShopLayout });
const props = defineProps({ order: Object, timeline: Array, payments: Array });
const { t } = useUiText();
</script>
<template>
  <Head :title="order.number" />
  <div class="mx-auto max-w-2xl px-4 py-8 space-y-4">
    <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ order.number }}</h1>
    <div class="rounded-3xl bg-white p-4 shadow-sm">{{ order.status_label }} · {{ order.total }} ج.م</div>
    <div class="space-y-2">
      <div v-for="h in timeline" :key="h.id" class="rounded-2xl bg-[var(--color-primary-soft)] p-3 text-sm">
        {{ h.to_status }} — {{ h.note }}
      </div>
    </div>
    <div class="rounded-3xl bg-white p-4 shadow-sm space-y-2">
      <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm">
        <span>{{ item.name_ar }} × {{ item.quantity }}</span>
        <span>{{ item.line_total }}</span>
      </div>
    </div>
    <div class="flex flex-wrap gap-2">
      <Link v-if="['pending_payment','payment_rejected'].includes(order.status)" :href="`/order/${order.number}/pay`" class="btn-primary">رفع إيصال</Link>
      <button class="btn-secondary" @click="router.post(`/account/orders/${order.number}/reorder`)">{{ t('account.reorder') }}</button>
    </div>
  </div>
</template>
