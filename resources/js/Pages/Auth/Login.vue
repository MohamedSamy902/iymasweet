<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';
defineOptions({ layout: ShopLayout });
const { t } = useUiText();
const form = useForm({ phone: '', password: '', remember: true });
</script>
<template>
    <Head :title="t('auth.login')" />
    <div class="mx-auto max-w-md px-4 py-10">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('auth.login') }}</h1>
        <form class="mt-6 space-y-3 rounded-3xl bg-white p-5 shadow-sm" @submit.prevent="form.post('/login')">
            <input v-model="form.phone" class="w-full rounded-2xl border px-4 py-3" :placeholder="t('auth.phone')" />
            <input v-model="form.password" type="password" class="w-full rounded-2xl border px-4 py-3" :placeholder="t('auth.password')" />
            <button class="btn-primary w-full" :disabled="form.processing">{{ t('auth.login') }}</button>
            <p class="text-sm">مش عندك حساب؟ <Link href="/register" class="text-[var(--color-denim)]">{{ t('auth.register') }}</Link></p>
            <p v-if="form.errors.phone" class="text-sm text-red-500">{{ form.errors.phone }}</p>
        </form>
    </div>
</template>
