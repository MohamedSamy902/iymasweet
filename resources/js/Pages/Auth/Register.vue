<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import FormField from '@/Components/Form/FormField.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const { t } = useUiText();
const form = useForm({ name: '', phone: '', email: '', password: '', password_confirmation: '' });
</script>

<template>
    <Head :title="t('auth.register')" />
    <div class="mx-auto max-w-md px-4 py-10">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('auth.register') }}</h1>
        <form class="surface-card mt-6 space-y-4 overflow-hidden p-5" @submit.prevent="form.post('/register')">
            <FormField :label="t('auth.name', 'الاسم')" required :error="form.errors.name">
                <input v-model="form.name" aria-required="true" autocomplete="name" />
            </FormField>
            <FormField :label="t('auth.phone')" required :error="form.errors.phone">
                <input v-model="form.phone" aria-required="true" inputmode="tel" autocomplete="tel" />
            </FormField>
            <FormField :label="t('auth.email_optional', 'البريد الإلكتروني')" optional :error="form.errors.email">
                <input v-model="form.email" type="email" autocomplete="email" />
            </FormField>
            <FormField :label="t('auth.password')" required :error="form.errors.password" hint="٨ حروف على الأقل">
                <input v-model="form.password" type="password" aria-required="true" autocomplete="new-password" minlength="8" />
            </FormField>
            <FormField :label="t('auth.password_confirm', 'تأكيد كلمة المرور')" required :error="form.errors.password_confirmation">
                <input v-model="form.password_confirmation" type="password" aria-required="true" autocomplete="new-password" minlength="8" />
            </FormField>
            <p v-if="form.hasErrors" class="field-error">راجعي الحقول المظللة وحاولي تاني.</p>
            <button class="btn-primary w-full" :disabled="form.processing">{{ t('auth.register') }}</button>
            <p class="text-sm">
                {{ t('auth.have_account', 'عندك حساب؟') }}
                <Link href="/login" class="font-semibold text-[var(--color-denim)]">{{ t('auth.login') }}</Link>
            </p>
        </form>
    </div>
</template>
