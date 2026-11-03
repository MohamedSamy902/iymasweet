<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import Sparkle from '@/Components/Ui/Sparkle.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({ order: Object });
const { t } = useUiText();

const copyNumber = async () => {
    try {
        await navigator.clipboard?.writeText(String(props.order.number || ''));
    } catch (_) {}
};
</script>

<template>
    <Head :title="t('checkout.success_title', 'تم استلام طلبك')" />
    <div class="container-shop max-w-lg py-14 text-center">
        <div class="relative mx-auto inline-block">
            <img src="/images/mascots.svg" alt="" class="mx-auto h-44 w-44 object-contain animate-float" />
            <span class="sparkle absolute -start-2 top-2"><Sparkle :size="18" /></span>
            <span class="sparkle sparkle--sun absolute -end-1 top-8"><Sparkle :size="14" tone="sun" /></span>
        </div>
        <h1 class="type-h1 mt-6">
            {{ t('checkout.success_title', 'وصّلنا طلبك، بنجهّزه بحب 🧁') }}
        </h1>
        <button
            type="button"
            class="mt-5 inline-flex items-center gap-2 rounded-full border border-dashed border-[var(--pink-300)] bg-[var(--pink-50)] px-5 py-3 font-bold text-[var(--pink-700)]"
            @click="copyNumber"
        >
            <span class="text-sm text-[var(--cocoa-500)]">{{ t('checkout.order_number', 'رقم الطلب') }}</span>
            <span class="dir-ltr text-lg">{{ order.number }}</span>
            <span class="text-xs">{{ t('common.copy', 'نسخ') }}</span>
        </button>
        <p class="mt-3 text-sm text-[var(--cocoa-500)]">
            {{ t('checkout.status', 'الحالة') }}:
            <span class="status-pill status-preparing ms-1">{{ order.status_label }}</span>
        </p>
        <Link :href="`/account/orders/${order.number}`" class="btn-primary mt-8 inline-flex">
            {{ t('checkout.track', 'تابع طلبك') }}
        </Link>
        <Link href="/shop" class="mt-4 block text-sm font-bold text-[var(--denim-700)]">
            {{ t('home.cta_see_menu', 'شوف المنيو') }}
        </Link>
    </div>
</template>
