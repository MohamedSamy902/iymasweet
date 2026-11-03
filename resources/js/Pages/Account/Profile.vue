<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import FormField from '@/Components/Form/FormField.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({ user: Object });
const { t } = useUiText();
const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
});
</script>

<template>
    <Head :title="t('account.profile')" />
    <form class="mx-auto max-w-md space-y-4 px-4 py-8" @submit.prevent="form.post('/account/profile')">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('account.profile') }}</h1>
        <div class="surface-card space-y-4 p-5">
            <FormField :label="t('auth.name', 'الاسم')" required :error="form.errors.name">
                <input v-model="form.name" aria-required="true" />
            </FormField>
            <FormField :label="t('auth.email_optional')" optional :error="form.errors.email">
                <input v-model="form.email" type="email" />
            </FormField>
            <FormField label="كلمة مرور جديدة" optional :error="form.errors.password">
                <input v-model="form.password" type="password" autocomplete="new-password" />
            </FormField>
            <FormField :label="t('auth.password_confirm')" optional>
                <input v-model="form.password_confirmation" type="password" autocomplete="new-password" />
            </FormField>
            <button class="btn-primary w-full" :disabled="form.processing">{{ t('common.save') }}</button>
        </div>
    </form>
</template>
