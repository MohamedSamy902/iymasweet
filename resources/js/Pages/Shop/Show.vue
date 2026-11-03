<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowRight, Share2, Heart } from '@lucide/vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import ProductGallery from '@/Components/Product/ProductGallery.vue';
import FormField from '@/Components/Form/FormField.vue';
import StatusBadge from '@/Components/Ui/StatusBadge.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({ product: Object, related: Array });
const { t } = useUiText();
const page = usePage();

const selectedVariant = ref(props.product.variants?.find(v => v.is_default)?.id || props.product.variants?.[0]?.id || null);
const quantity = ref(props.product.min_qty || 1);
const selectedAddons = ref([]);
const customization = reactive({});
const tab = ref('description');

const unitPrice = computed(() => {
    const variant = props.product.variants?.find(v => v.id === selectedVariant.value);
    let price = Number(variant?.price ?? props.product.base_price);
    selectedAddons.value.forEach(a => { price += Number(a.price || 0); });
    return price;
});

const total = computed(() => unitPrice.value * quantity.value);
const compare = computed(() => props.product.compare_at_price);
const discountPct = computed(() => {
    if (!compare.value || compare.value <= unitPrice.value) return null;
    return Math.round(((compare.value - unitPrice.value) / compare.value) * 100);
});

const availabilityHint = computed(() => {
    const type = props.product.availability_type;
    if (type === 'available_now') return t('product.hint_now', 'متاح دلوقتي — بيوصلك النهاردة لو طلبت قبل ٨ بالليل');
    if (type === 'sold_out') return t('product.hint_sold', 'خلص النهاردة');
    if (type === 'available_tomorrow') return t('product.hint_tomorrow', 'متاح بكرة — احجز مكانك دلوقتي');
    return t('product.lead_time', 'بيتجهز في {hours} ساعة', { hours: props.product.lead_time_hours || 48 });
});

const addToCart = () => {
    try { navigator.vibrate?.(10); } catch (_) {}
    router.post('/cart', {
        product_id: props.product.id,
        variant_id: selectedVariant.value,
        quantity: quantity.value,
        addons: selectedAddons.value,
        customization,
    }, { preserveScroll: true });
};

const toggleAddon = (opt) => {
    const idx = selectedAddons.value.findIndex(a => a.id === opt.id);
    if (idx >= 0) selectedAddons.value.splice(idx, 1);
    else selectedAddons.value.push({ id: opt.id, name_ar: opt.name_ar, price: opt.price });
};

const notify = () => {
    if (!page.props.auth?.user) {
        router.visit('/login');
        return;
    }
    router.post(`/account/stock-alerts/${props.product.id}`);
};
</script>

<template>
    <Head :title="product.name_ar" />
    <div class="pb-28 lg:container-shop lg:py-8">
        <div class="relative lg:grid lg:grid-cols-[55%_45%] lg:items-start lg:gap-10">
            <div class="relative">
                <div class="absolute start-3 top-3 z-10 flex gap-2 lg:hidden">
                    <Link href="/shop" class="btn-ghost !bg-white/85 !shadow-[var(--sh-sm)] backdrop-blur" :aria-label="t('common.back', 'رجوع')">
                        <ArrowRight class="h-5 w-5 rtl:rotate-0" :stroke-width="1.75" />
                    </Link>
                </div>
                <div class="absolute end-3 top-3 z-10 flex gap-2 lg:hidden">
                    <button type="button" class="btn-ghost !bg-white/85 !shadow-[var(--sh-sm)] backdrop-blur" :aria-label="t('common.share', 'مشاركة')">
                        <Share2 class="h-5 w-5" :stroke-width="1.75" />
                    </button>
                    <button type="button" class="btn-ghost !bg-white/85 !shadow-[var(--sh-sm)] backdrop-blur" :aria-label="t('product.favorite', 'المفضلة')">
                        <Heart class="h-5 w-5" :stroke-width="1.75" />
                    </button>
                </div>
                <div class="lg:sticky lg:top-24">
                    <ProductGallery :media="product.images || []" :alt="product.name_ar" />
                </div>
            </div>

            <div class="relative z-[1] -mt-6 space-y-5 rounded-t-[28px] bg-[var(--surface)] px-4 pb-8 pt-6 shadow-[var(--sh-md)] lg:mt-0 lg:rounded-[var(--r-lg)] lg:px-6 lg:shadow-[var(--sh-sm)]">
                <div class="mx-auto mb-2 h-1 w-10 rounded-full bg-[var(--cocoa-100)] lg:hidden" aria-hidden="true" />

                <div class="text-sm text-[var(--cocoa-500)]">
                    <Link href="/shop" class="hover:text-[var(--pink-700)]">{{ t('shop.title') }}</Link>
                    <span v-if="product.category"> · {{ product.category.name_ar }}</span>
                </div>

                <h1 class="type-h1">{{ product.name_ar }}</h1>

                <div class="flex flex-wrap items-center gap-3">
                    <StatusBadge :type="product.availability_type" :label="product.availability_label" />
                    <span class="text-sm font-semibold text-[var(--cocoa-500)]">
                        ★ <span class="dir-ltr">{{ product.avg_rating }}</span>
                        ({{ product.ratings_count }})
                    </span>
                </div>

                <div class="flex flex-wrap items-baseline gap-3">
                    <div class="font-heading text-[32px] font-extrabold text-[var(--pink-700)] dir-ltr">
                        {{ total }} <span class="text-[18px]">ج.م</span>
                    </div>
                    <span v-if="compare" class="dir-ltr text-[14px] text-[var(--cocoa-500)] line-through">{{ compare }} ج.م</span>
                    <span v-if="discountPct" class="rounded-full bg-[var(--sun)] px-2.5 py-1 text-[12px] font-bold text-[var(--cocoa-900)]">
                        −{{ discountPct }}%
                    </span>
                </div>

                <div class="availability-block" :class="`availability-block--${product.availability_type}`">
                    <div>
                        <div class="font-bold text-[var(--cocoa-900)]">{{ product.availability_label }}</div>
                        <p class="mt-1 text-sm text-[var(--cocoa-700)]">{{ availabilityHint }}</p>
                        <button
                            v-if="product.availability_type==='sold_out'"
                            type="button"
                            class="btn-secondary mt-3 !min-h-10 !px-4 !text-sm"
                            @click="notify"
                        >
                            {{ t('product.notify_me') }}
                        </button>
                    </div>
                </div>

                <div v-if="product.variants?.length" class="space-y-3">
                    <div class="field-label">
                        {{ t('product.size', 'الحجم') }}
                        <span class="field-required">*</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            v-for="v in product.variants"
                            :key="v.id"
                            type="button"
                            class="rounded-[var(--r-md)] border-2 p-3 text-start transition"
                            :class="selectedVariant===v.id
                                ? 'border-[var(--pink-500)] bg-[var(--pink-50)]'
                                : 'border-[var(--cocoa-100)] bg-[var(--surface)]'"
                            @click="selectedVariant=v.id"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-bold">{{ v.name_ar }}</span>
                                <span v-if="selectedVariant===v.id" class="text-[var(--pink-600)]">✓</span>
                            </div>
                            <div class="mt-1 dir-ltr text-xs font-semibold text-[var(--cocoa-500)]">{{ v.price }} ج.م</div>
                        </button>
                    </div>
                </div>

                <div v-for="group in product.addon_groups || []" :key="group.id" class="space-y-2">
                    <div class="field-label">
                        {{ group.name_ar }}
                        <span v-if="group.is_required" class="field-required">*</span>
                        <span v-else class="field-optional">اختياري</span>
                    </div>
                    <div class="overflow-hidden rounded-[var(--r-md)] border border-[var(--cocoa-100)]">
                        <button
                            v-for="opt in group.options"
                            :key="opt.id"
                            type="button"
                            class="flex h-14 w-full items-center justify-between gap-3 border-b border-[var(--cocoa-100)] px-4 last:border-b-0"
                            @click="toggleAddon(opt)"
                        >
                            <span class="flex items-center gap-3 text-sm font-semibold">
                                <span
                                    class="flex h-5 w-5 items-center justify-center rounded-md border-2"
                                    :class="selectedAddons.some(a=>a.id===opt.id)
                                        ? 'border-[var(--pink-600)] bg-[var(--pink-600)] text-white'
                                        : 'border-[var(--cocoa-300)]'"
                                >
                                    <span v-if="selectedAddons.some(a=>a.id===opt.id)">✓</span>
                                </span>
                                {{ opt.name_ar }}
                            </span>
                            <span class="dir-ltr text-sm font-bold text-[var(--pink-700)]">+{{ opt.price }}</span>
                        </button>
                    </div>
                </div>

                <div v-if="product.allow_customization && product.customization_config" class="space-y-3">
                    <FormField
                        v-for="field in product.customization_config"
                        :key="field.key"
                        :label="field.label_ar"
                        :required="!!field.required"
                        :optional="!field.required"
                    >
                        <input
                            v-if="field.type==='text'"
                            v-model="customization[field.key]"
                            :aria-required="field.required ? 'true' : 'false'"
                            :maxlength="field.max || 40"
                        />
                        <textarea
                            v-else
                            v-model="customization[field.key]"
                            :aria-required="field.required ? 'true' : 'false'"
                            rows="2"
                        />
                    </FormField>
                </div>

                <FormField :label="t('product.quantity', 'الكمية')" required>
                    <div class="qty-stepper">
                        <button type="button" aria-label="−" @click="quantity=Math.max(product.min_qty||1, quantity-1)">−</button>
                        <span>{{ quantity }}</span>
                        <button type="button" aria-label="+" @click="quantity++">+</button>
                    </div>
                </FormField>

                <div class="h-scroll gap-3 border-b border-[var(--cocoa-100)]">
                    <button
                        v-for="key in ['description','ingredients','allergens','storage','reviews']"
                        :key="key"
                        class="shrink-0 pb-3 text-sm font-semibold"
                        :class="tab===key ? 'border-b-2 border-[var(--pink-600)] text-[var(--pink-700)]' : 'text-[var(--cocoa-500)]'"
                        @click="tab=key"
                    >
                        {{ t('product.'+key) }}
                    </button>
                </div>
                <div class="text-[15px] leading-[1.75] text-[var(--cocoa-700)]">
                    <div v-if="tab==='description'" v-html="product.description_ar" />
                    <div v-else-if="tab==='ingredients'">{{ product.ingredients_ar }}</div>
                    <div v-else-if="tab==='allergens'">{{ product.allergens_ar }}</div>
                    <div v-else-if="tab==='storage'">{{ product.storage_instructions_ar }}</div>
                    <div v-else class="space-y-3">
                        <div v-for="r in product.reviews" :key="r.id" class="surface-card p-4">
                            <div class="font-semibold">{{ r.user?.name }} · ★ {{ r.rating }}</div>
                            <p class="text-[var(--cocoa-500)]">{{ r.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section v-if="related?.length" class="container-shop mt-10 space-y-4 px-4 lg:px-0">
            <h2 class="section-title mb-0">{{ t('product.related', 'منتجات مشابهة') }}</h2>
            <span class="section-underline" />
            <div class="product-grid mt-5">
                <ProductCard v-for="p in related" :key="p.id" :product="p" />
            </div>
        </section>

        <div
            class="fixed inset-x-0 z-[55] border-t border-[var(--cocoa-100)] bg-[var(--surface)]/95 p-3 backdrop-blur md:bottom-0"
            style="bottom: calc(4rem + env(safe-area-inset-bottom, 0px))"
        >
            <div class="mx-auto flex h-[72px] max-w-[1200px] items-center gap-3 px-1">
                <div class="min-w-0 flex-1">
                    <div class="text-[12px] font-semibold text-[var(--cocoa-500)]">{{ t('cart.total', 'الإجمالي') }}</div>
                    <div class="dir-ltr font-heading text-xl font-extrabold text-[var(--pink-700)]">{{ total }} ج.م</div>
                </div>
                <button
                    v-if="product.availability_type!=='sold_out'"
                    type="button"
                    class="btn-primary flex-1 !min-h-12"
                    @click="addToCart"
                >
                    {{ product.availability_type==='available_tomorrow' ? t('product.book_tomorrow') : (product.availability_type==='pre_order' ? t('product.preorder') : t('product.add_to_cart')) }}
                </button>
                <button v-else type="button" class="btn-secondary flex-1" @click="notify">{{ t('product.notify_me') }}</button>
            </div>
        </div>
    </div>
</template>
