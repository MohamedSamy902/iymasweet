<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';
defineOptions({ layout: ShopLayout });
defineProps({ user: Object, stats: Object });
const { t } = useUiText();
</script>
<template>
  <Head :title="t('account.title')" />
  <div class="mx-auto max-w-lg px-4 py-8 space-y-4">
    <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('account.title') }}</h1>
    <div class="rounded-3xl bg-white p-5 shadow-sm">{{ t('account.hello', 'أهلاً، {name}').replace('{name}', user.name) }}</div>
    <Link href="/account/orders" class="block rounded-3xl bg-white p-4 shadow-sm">{{ t('account.orders') }} ({{ stats.orders }})</Link>
    <Link href="/account/favorites" class="block rounded-3xl bg-white p-4 shadow-sm">{{ t('account.favorites') }} ({{ stats.favorites }})</Link>
    <Link href="/account/profile" class="block rounded-3xl bg-white p-4 shadow-sm">{{ t('account.profile') }}</Link>
    <button class="btn-secondary w-full" @click="router.post('/logout')">{{ t('auth.logout') }}</button>
  </div>
</template>
