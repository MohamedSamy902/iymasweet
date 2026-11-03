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
    <div class="container-shop grid items-center gap-10 py-10 lg:min-h-[70vh] lg:grid-cols-2">
        <div class="hidden place-items-center rounded-[var(--r-xl)] bg-[var(--pink-100)] p-10 lg:grid">
            <img src="/images/mascots.svg" alt="" class="h-64 w-64 object-contain animate-float" />
        </div>
        <div class="mx-auto w-full max-w-md">
            <div class="segmented mb-6 w-full">
                <Link href="/login" class="segmented-item flex-1 text-center">{{ t('auth.login') }}</Link>
                <Link href="/register" class="segmented-item is-active flex-1 text-center">{{ t('auth.register') }}</Link>
            </div>
            <h1 class="type-h1">{{ t('auth.register') }}</h1>
            <form class="surface-card mt-6 space-y-4 overflow-hidden p-5" @submit.prevent="form.post('/register')">
                <FormField :label="t('auth.name', 'الاسم')" required :error="form.errors.name">
                    <input v-model="form.name" aria-required="true" autocomplete="name" />
                </FormField>
                <FormField :label="t('auth.phone')" required :error="form.errors.phone">
                    <div class="flex overflow-hidden rounded-[var(--r-md)] border border-[var(--cocoa-100)] focus-within:border-[var(--pink-500)] focus-within:shadow-[var(--ring)]">
                        <span class="flex items-center bg-[var(--pink-50)] px-3 text-sm font-bold text-[var(--cocoa-700)] dir-ltr">+20</span>
                        <input v-model="form.phone" class="!rounded-none !border-0 !shadow-none" aria-required="true" inputmode="tel" autocomplete="tel" dir="ltr" />
                    </div>
                </FormField>
                <FormField :label="t('auth.email_optional', 'البريد الإلكتروني')" optional :error="form.errors.email">
                    <input v-model="form.email" type="email" autocomplete="email" dir="ltr" />
                </FormField>
                <FormField :label="t('auth.password')" required :error="form.errors.password" hint="٨ حروف على الأقل">
                    <input v-model="form.password" type="password" aria-required="true" autocomplete="new-password" minlength="8" />
                </FormField>
                <FormField :label="t('auth.password_confirm', 'تأكيد كلمة المرور')" required :error="form.errors.password_confirmation">
                    <input v-model="form.password_confirmation" type="password" aria-required="true" autocomplete="new-password" minlength="8" />
                </FormField>
                <p v-if="form.hasErrors" class="field-error" role="alert">{{ t('auth.fix_error', 'راجعي الحقول المظللة وحاولي تاني.') }}</p>
                <button class="btn-primary w-full" :disabled="form.processing">{{ t('auth.register') }}</button>
                <Link href="/checkout" class="block text-center text-sm font-bold text-[var(--denim-700)]">
                    {{ t('auth.continue_guest', 'كمّل كضيف') }}
                </Link>
            </form>
        </div>
    </div>
</template>
