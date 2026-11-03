<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Heart, Plus, Star } from '@lucide/vue';
import { useUiText } from '@/Composables/useUiText';
import ProductImage from '@/Components/Ui/ProductImage.vue';
import StatusBadge from '@/Components/Ui/StatusBadge.vue';

const props = defineProps({ product: { type: Object, required: true } });
const { t } = useUiText();

const soldOut = computed(() => props.product.availability_type === 'sold_out');

const categoryKey = computed(() => {
    const slug = String(props.product.category?.slug || props.product.category_slug || '').toLowerCase();
    if (slug.includes('cake') || slug.includes('tort') || slug.includes('تورت')) return 'cake';
    if (slug.includes('cookie') || slug.includes('كوك')) return 'cookie';
    if (slug.includes('box') || slug.includes('بوكس') || slug.includes('bundle')) return 'box';
    return 'donut';
});

const add = () => {
    if (soldOut.value) return;
    try {
        navigator.vibrate?.(10);
    } catch (_) {}
    router.post('/cart', { product_id: props.product.id, quantity: 1 }, { preserveScroll: true });
};
</script>

<template>
    <article class="product-card group">
        <Link :href="`/product/${product.slug}`" class="relative block">
            <ProductImage
                :src="product.image"
                :alt="product.name_ar"
                :category="categoryKey"
                :sold-out="soldOut"
            />
            <div class="absolute start-2.5 top-2.5 z-10">
                <StatusBadge :type="product.availability_type" :label="product.availability_label" />
            </div>
            <button
                type="button"
                class="absolute end-2.5 top-2.5 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/80 text-[var(--cocoa-500)] backdrop-blur-sm transition hover:text-[var(--pink-600)]"
                :aria-label="t('product.favorite', 'المفضلة')"
                @click.prevent
            >
                <Heart class="h-[18px] w-[18px]" :stroke-width="1.75" />
            </button>
        </Link>

        <div class="space-y-2 px-2 pb-1 pt-2">
            <Link
                :href="`/product/${product.slug}`"
                class="type-h3 line-clamp-2 !text-[15px] text-[var(--cocoa-900)]"
            >
                {{ product.name_ar }}
            </Link>

            <div v-if="product.avg_rating" class="flex items-center gap-1 text-[12px] font-semibold text-[var(--cocoa-500)]">
                <Star class="h-3.5 w-3.5 fill-[var(--sun)] text-[var(--sun)]" :stroke-width="0" />
                <span class="dir-ltr">{{ product.avg_rating }}</span>
            </div>

            <div class="flex items-center justify-between gap-2 pt-0.5">
                <div class="min-w-0">
                    <div class="type-price dir-ltr truncate">
                        {{ product.base_price }}
                        <span class="text-[13px]">{{ t('common.currency', 'ج.م') }}</span>
                    </div>
                    <div
                        v-if="product.compare_at_price"
                        class="dir-ltr text-[12px] text-[var(--cocoa-500)] line-through"
                    >
                        {{ product.compare_at_price }}
                    </div>
                </div>

                <button
                    v-if="!soldOut"
                    type="button"
                    class="btn-icon-add shrink-0"
                    :aria-label="t('product.add_to_cart', 'أضف للسلة')"
                    @click="add"
                >
                    <Plus class="h-5 w-5" :stroke-width="2.25" />
                </button>
                <Link
                    v-else
                    :href="`/product/${product.slug}`"
                    class="shrink-0 rounded-full border border-[var(--pink-300)] px-3 py-2 text-[12px] font-bold text-[var(--pink-700)]"
                >
                    {{ t('product.notify_me', 'نبّهني') }}
                </Link>
            </div>
        </div>
    </article>
</template>
