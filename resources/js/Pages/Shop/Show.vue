<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import ProductGallery from '@/Components/Product/ProductGallery.vue';
import FormField from '@/Components/Form/FormField.vue';
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

const addToCart = () => {
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
    <div class="mx-auto max-w-6xl px-4 py-6 pb-28">
        <div class="grid gap-8 md:grid-cols-2 md:items-start">
            <ProductGallery :media="product.images || []" :alt="product.name_ar" />

            <div class="space-y-5">
                <div class="text-sm text-[var(--color-text-muted)]">
                    <Link href="/shop" class="hover:text-[var(--color-denim)]">{{ t('shop.title') }}</Link>
                    <span v-if="product.category"> / {{ product.category.name_ar }}</span>
                </div>
                <h1 class="font-heading text-3xl text-[var(--color-brown)] md:text-4xl">{{ product.name_ar }}</h1>
                <p v-if="product.short_description_ar" class="text-[var(--color-text-muted)]">{{ product.short_description_ar }}</p>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-[var(--color-primary-soft)] px-3 py-1 text-xs font-bold text-[var(--color-primary-dark)]">
                        {{ product.availability_label }}
                    </span>
                    <span class="text-sm text-[var(--color-text-muted)]">★ {{ product.avg_rating }} ({{ product.ratings_count }})</span>
                </div>
                <div class="text-3xl font-bold tracking-tight text-[var(--color-primary-dark)]">{{ total }} ج.م</div>
                <p v-if="product.availability_type==='pre_order'" class="text-sm text-[var(--color-text-muted)]">
                    {{ t('product.lead_time', null, { hours: product.lead_time_hours || 48 }) }}
                </p>

                <div v-if="product.variants?.length" class="space-y-2">
                    <div class="field-label">
                        {{ t('product.size', 'الحجم') }}
                        <span class="field-required">*</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="v in product.variants"
                            :key="v.id"
                            type="button"
                            class="chip"
                            :class="{ 'chip-active': selectedVariant===v.id }"
                            @click="selectedVariant=v.id"
                        >
                            {{ v.name_ar }} — {{ v.price }}
                        </button>
                    </div>
                </div>

                <div v-for="group in product.addon_groups || []" :key="group.id" class="space-y-2">
                    <div class="field-label">
                        {{ group.name_ar }}
                        <span v-if="group.is_required" class="field-required">*</span>
                        <span v-else class="field-optional">اختياري</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="opt in group.options"
                            :key="opt.id"
                            type="button"
                            class="chip"
                            :class="{ 'chip-active': selectedAddons.some(a=>a.id===opt.id) }"
                            @click="toggleAddon(opt)"
                        >
                            {{ opt.name_ar }} +{{ opt.price }}
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
                    <div class="flex items-center gap-3">
                        <button type="button" class="rounded-full bg-[var(--color-denim-soft)] px-3 py-1 font-bold" @click="quantity=Math.max(product.min_qty||1, quantity-1)">−</button>
                        <span class="min-w-8 text-center font-bold">{{ quantity }}</span>
                        <button type="button" class="rounded-full bg-[var(--color-denim-soft)] px-3 py-1 font-bold" @click="quantity++">+</button>
                    </div>
                </FormField>
            </div>
        </div>

        <div class="mt-10 h-scroll gap-3 border-b border-black/5">
            <button
                v-for="key in ['description','ingredients','allergens','storage','reviews']"
                :key="key"
                class="shrink-0 pb-3 text-sm font-semibold"
                :class="tab===key ? 'border-b-2 border-[var(--color-primary)] text-[var(--color-primary-dark)]' : 'text-[var(--color-text-muted)]'"
                @click="tab=key"
            >
                {{ t('product.'+key) }}
            </button>
        </div>
        <div class="mt-4 text-sm leading-7 text-[var(--color-text)]">
            <div v-if="tab==='description'" v-html="product.description_ar" />
            <div v-else-if="tab==='ingredients'">{{ product.ingredients_ar }}</div>
            <div v-else-if="tab==='allergens'">{{ product.allergens_ar }}</div>
            <div v-else-if="tab==='storage'">{{ product.storage_instructions_ar }}</div>
            <div v-else class="space-y-3">
                <div v-for="r in product.reviews" :key="r.id" class="surface-card p-4">
                    <div class="font-semibold">{{ r.user?.name }} · ★ {{ r.rating }}</div>
                    <p class="text-[var(--color-text-muted)]">{{ r.comment }}</p>
                </div>
            </div>
        </div>

        <section v-if="related?.length" class="mt-10 space-y-4">
            <h2 class="font-heading text-2xl text-[var(--color-brown)]">{{ t('product.related') }}</h2>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <ProductCard v-for="p in related" :key="p.id" :product="p" />
            </div>
        </section>

        <div class="fixed inset-x-0 z-[55] border-t border-black/5 bg-white/95 p-3 backdrop-blur md:bottom-0" style="bottom: calc(3.75rem + env(safe-area-inset-bottom, 0px))">
            <div class="mx-auto flex max-w-6xl gap-3">
                <button v-if="product.availability_type!=='sold_out'" type="button" class="btn-primary flex-1" @click="addToCart">
                    {{ product.availability_type==='available_tomorrow' ? t('product.book_tomorrow') : (product.availability_type==='pre_order' ? t('product.preorder') : t('product.add_to_cart')) }}
                    — {{ total }} ج.م
                </button>
                <button v-else type="button" class="btn-secondary flex-1" @click="notify">{{ t('product.notify_me') }}</button>
            </div>
        </div>
    </div>
</template>
