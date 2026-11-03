<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
defineOptions({ layout: ShopLayout });
const props = defineProps({ page: Object, faqs: Array });
const form = useForm({ name: '', phone: '', message: '' });
</script>
<template>
  <Head :title="page.title_ar" />
  <div class="mx-auto max-w-3xl px-4 py-8 prose prose-neutral">
    <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ page.title_ar }}</h1>
    <div class="mt-4" v-html="page.content_ar" />
    <div v-if="faqs?.length" class="mt-8 space-y-3 not-prose">
      <details v-for="f in faqs" :key="f.id" class="rounded-2xl bg-white p-4 shadow-sm">
        <summary class="font-semibold">{{ f.question_ar }}</summary>
        <p class="mt-2 text-sm text-[var(--color-text-muted)]">{{ f.answer_ar }}</p>
      </details>
    </div>
    <form v-if="page.slug==='contact'" class="mt-8 space-y-3 not-prose" @submit.prevent="form.post('/contact')">
      <input v-model="form.name" class="w-full rounded-2xl border px-4 py-2" placeholder="الاسم" />
      <input v-model="form.phone" class="w-full rounded-2xl border px-4 py-2" placeholder="موبايل" />
      <textarea v-model="form.message" class="w-full rounded-2xl border px-4 py-2" rows="4" placeholder="رسالتك" />
      <button class="btn-primary">إرسال</button>
    </form>
  </div>
</template>
