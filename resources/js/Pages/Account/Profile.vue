<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
defineOptions({ layout: ShopLayout });
const props = defineProps({ user: Object });
const form = useForm({ name: props.user.name, email: props.user.email || '', password: '', password_confirmation: '' });
</script>
<template>
  <Head title="بياناتي" />
  <form class="mx-auto max-w-md px-4 py-8 space-y-3" @submit.prevent="form.post('/account/profile')">
    <input v-model="form.name" class="w-full rounded-2xl border px-4 py-3" />
    <input v-model="form.email" class="w-full rounded-2xl border px-4 py-3" placeholder="إيميل" />
    <input v-model="form.password" type="password" class="w-full rounded-2xl border px-4 py-3" placeholder="كلمة سر جديدة" />
    <input v-model="form.password_confirmation" type="password" class="w-full rounded-2xl border px-4 py-3" placeholder="تأكيد" />
    <button class="btn-primary w-full">حفظ</button>
  </form>
</template>
