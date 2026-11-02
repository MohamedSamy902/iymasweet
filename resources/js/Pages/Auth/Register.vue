<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';
defineOptions({ layout: ShopLayout });
const { t } = useUiText();
const form = useForm({ name: '', phone: '', email: '', password: '', password_confirmation: '' });
</script>
<template>
    <Head :title="t('auth.register')" />
    <div class="mx-auto max-w-md px-4 py-10">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('auth.register') }}</h1>
        <form class="mt-6 space-y-3 rounded-3xl bg-white p-5 shadow-sm" @submit.prevent="form.post('/register')">
            <input v-model="form.name" class="w-full rounded-2xl border px-4 py-3" placeholder="الاسم" />
            <input v-model="form.phone" class="w-full rounded-2xl border px-4 py-3" :placeholder="t('auth.phone')" />
            <input v-model="form.email" class="w-full rounded-2xl border px-4 py-3" placeholder="إيميل (اختياري)" />
            <input v-model="form.password" type="password" class="w-full rounded-2xl border px-4 py-3" :placeholder="t('auth.password')" />
            <input v-model="form.password_confirmation" type="password" class="w-full rounded-2xl border px-4 py-3" placeholder="تأكيد كلمة السر" />
            <button class="btn-primary w-full" :disabled="form.processing">{{ t('auth.register') }}</button>
            <p class="text-sm">عندك حساب؟ <Link href="/login" class="text-[var(--color-denim)]">{{ t('auth.login') }}</Link></p>
        </form>
    </div>
</template>
