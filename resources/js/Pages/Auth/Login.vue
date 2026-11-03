<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import FormField from '@/Components/Form/FormField.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const { t } = useUiText();
const form = useForm({ phone: '', password: '', remember: true });
</script>

<template>
    <Head :title="t('auth.login')" />
    <div class="mx-auto max-w-md px-4 py-10">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('auth.login') }}</h1>
        <form class="surface-card mt-6 space-y-4 p-5" @submit.prevent="form.post('/login')">
            <FormField :label="t('auth.phone')" required :error="form.errors.phone">
                <input v-model="form.phone" aria-required="true" inputmode="tel" autocomplete="tel" />
            </FormField>
            <FormField :label="t('auth.password')" required :error="form.errors.password">
                <input v-model="form.password" type="password" aria-required="true" autocomplete="current-password" />
            </FormField>
            <label class="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
                <input v-model="form.remember" type="checkbox" />
                تذكّرني
            </label>
            <button class="btn-primary w-full" :disabled="form.processing">{{ t('auth.login') }}</button>
            <p class="text-sm">
                {{ t('auth.no_account', 'ليس لديك حساب؟') }}
                <Link href="/register" class="font-semibold text-[var(--color-denim)]">{{ t('auth.register') }}</Link>
            </p>
        </form>
    </div>
</template>
