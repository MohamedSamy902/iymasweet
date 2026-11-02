<script setup>
import { Link, router } from '@inertiajs/vue3';
import { useUiText } from '@/Composables/useUiText';
const props = defineProps({ product: { type: Object, required: true } });
const { t } = useUiText();
const add = () => {
  if (props.product.availability_type === 'sold_out') return;
  router.post('/cart', { product_id: props.product.id, quantity: 1 }, { preserveScroll: true });
};
</script>
<template>
  <article class="group overflow-hidden rounded-3xl bg-white/90 shadow-sm transition duration-200 hover:-translate-y-1">
    <Link :href="`/product/${product.slug}`" class="block overflow-hidden">
      <img :src="product.image || '/images/logo-placeholder.svg'" :alt="product.name_ar" class="aspect-square w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy" />
    </Link>
    <div class="space-y-2 p-3">
      <div class="flex items-start justify-between gap-2">
        <Link :href="`/product/${product.slug}`" class="font-semibold text-[var(--color-brown)]">{{ product.name_ar }}</Link>
        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold" :class="{
          'bg-[var(--color-mint)]/50': product.availability_type==='available_now',
          'bg-[var(--color-sky)]/50': product.availability_type==='available_tomorrow',
          'bg-[var(--color-lilac)]/50': product.availability_type==='pre_order',
          'bg-gray-200 text-[var(--color-text-muted)]': product.availability_type==='sold_out',
        }">{{ product.availability_label }}</span>
      </div>
      <div class="flex items-center gap-2">
        <span class="font-bold text-[var(--color-primary-dark)]">{{ product.base_price }} {{ t('common.currency','ج.م') }}</span>
        <span v-if="product.compare_at_price" class="text-xs text-[var(--color-text-muted)] line-through">{{ product.compare_at_price }}</span>
      </div>
      <button v-if="product.availability_type!=='sold_out'" type="button" class="btn-primary w-full !py-2 text-xs" @click="add">{{ t('product.add_to_cart','أضف للسلة') }}</button>
      <Link v-else :href="`/product/${product.slug}`" class="btn-secondary block w-full !py-2 text-center text-xs">{{ t('product.notify_me','نبّهني لما يتوفر') }}</Link>
    </div>
  </article>
</template>
