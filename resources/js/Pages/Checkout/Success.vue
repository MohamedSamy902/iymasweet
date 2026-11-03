<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({ order: Object });
const { t } = useUiText();
</script>

<template>
    <Head :title="t('checkout.success_title', 'تم استلام طلبك')" />
    <div class="mx-auto max-w-lg px-4 py-16 text-center">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">
            {{ t('checkout.success_title', 'تم استلام طلبك') }}
        </h1>
        <p class="mt-3 text-[var(--color-text-muted)]">
            رقم الطلب: <strong>{{ order.number }}</strong>
        </p>
        <p class="mt-1 text-sm text-[var(--color-text-muted)]">الحالة: {{ order.status_label }}</p>
        <Link :href="`/account/orders/${order.number}`" class="btn-primary mt-8 inline-flex">
            {{ t('checkout.track', 'متابعة الطلب') }}
        </Link>
    </div>
</template>
