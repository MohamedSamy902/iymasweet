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
  <article class="group overflow-hidden rounded-2xl border border-black/[0.04] bg-white/95 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
    <Link :href="`/product/${product.slug}`" class="relative block overflow-hidden bg-[var(--color-primary-soft)]/40">
      <img :src="product.image || '/images/logo-placeholder.svg'" :alt="product.name_ar" class="aspect-square w-full bg-[#f6e9ee] object-cover transition duration-500 group-hover:scale-[1.04]" loading="eager" decoding="async" />
      <span class="absolute start-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-bold text-[var(--color-brown)] shadow-sm" :class="{
          'text-emerald-700': product.availability_type==='available_now',
          'text-sky-700': product.availability_type==='available_tomorrow',
          'text-violet-700': product.availability_type==='pre_order',
          'text-[var(--color-text-muted)]': product.availability_type==='sold_out',
        }">{{ product.availability_label }}</span>
    </Link>
    <div class="space-y-2.5 p-3.5">
      <Link :href="`/product/${product.slug}`" class="line-clamp-2 text-sm font-semibold leading-snug text-[var(--color-brown)]">{{ product.name_ar }}</Link>
      <div class="flex items-baseline gap-2">
        <span class="text-base font-bold text-[var(--color-primary-dark)]">{{ product.base_price }} {{ t('common.currency','ج.م') }}</span>
        <span v-if="product.compare_at_price" class="text-xs text-[var(--color-text-muted)] line-through">{{ product.compare_at_price }}</span>
      </div>
      <button v-if="product.availability_type!=='sold_out'" type="button" class="btn-primary w-full !py-2.5 text-xs" @click="add">{{ t('product.add_to_cart','أضف للسلة') }}</button>
      <Link v-else :href="`/product/${product.slug}`" class="btn-secondary block w-full !py-2.5 text-center text-xs">{{ t('product.notify_me','نبّهني عند التوفر') }}</Link>
    </div>
  </article>
</template>
