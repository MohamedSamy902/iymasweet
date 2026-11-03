<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';
defineOptions({ layout: ShopLayout });
const props = defineProps({ order: Object, method: Object });
const { t } = useUiText();
const form = useForm({ receipt: null, sender_name: '', sender_phone_or_reference: '' });
const onFile = (e) => { form.receipt = e.target.files[0]; };
const submit = () => form.post(`/order/${props.order.number}/receipt`, { forceFormData: true });
const copy = async () => {
    await navigator.clipboard.writeText(props.method?.account_number || '');
};
</script>
<template>
    <Head :title="t('checkout.pay_and_upload')" />
    <div class="mx-auto max-w-lg px-4 py-8 space-y-4">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('checkout.pay_and_upload') }}</h1>
        <div class="rounded-3xl bg-white p-5 shadow-sm space-y-2">
            <div>طلب {{ order.number }}</div>
            <div class="text-2xl font-bold text-[var(--color-primary-dark)]">{{ order.total }} ج.م</div>
            <div>{{ method?.name_ar }}</div>
            <div class="flex items-center gap-2 font-mono text-lg">
                <span>{{ method?.account_number }}</span>
                <button type="button" class="btn-secondary !py-1 !px-3 text-xs" @click="copy">{{ t('checkout.copy_number') }}</button>
            </div>
            <p class="text-sm text-[var(--color-text-muted)]">{{ method?.instructions_ar }}</p>
        </div>
        <form class="rounded-3xl bg-white p-5 shadow-sm space-y-3" @submit.prevent="submit">
            <input type="file" accept="image/*" capture="environment" @change="onFile" class="w-full text-sm" />
            <input v-model="form.sender_name" class="w-full rounded-2xl border px-4 py-2" placeholder="اسم المحوّل" />
            <input v-model="form.sender_phone_or_reference" class="w-full rounded-2xl border px-4 py-2" placeholder="رقم العملية" />
            <button class="btn-primary w-full" :disabled="form.processing">{{ t('checkout.upload_receipt') }}</button>
        </form>
    </div>
</template>
