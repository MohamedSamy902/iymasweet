<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
defineOptions({ layout: ShopLayout });
defineProps({ order: Object });
</script>
<template>
    <Head title="تم الطلب" />
    <div class="mx-auto max-w-lg px-4 py-16 text-center">
        <div class="text-6xl animate-float">🍩</div>
        <h1 class="mt-4 font-heading text-3xl text-[var(--color-brown)]">طلبك اتأكد ✨</h1>
        <p class="mt-2 text-[var(--color-text-muted)]">رقم الطلب: <strong>{{ order.number }}</strong></p>
        <p class="mt-1">الحالة: {{ order.status_label }}</p>
        <Link :href="`/account/orders/${order.number}`" class="btn-primary mt-6 inline-flex">تتبع الطلب</Link>
    </div>
</template>
