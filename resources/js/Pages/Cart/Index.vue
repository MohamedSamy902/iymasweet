<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Trash2 } from '@lucide/vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({ cart: Object });
const { t } = useUiText();
const code = ref(props.cart.summary?.coupon_code || '');

const updateQty = (id, quantity) => router.patch(`/cart/items/${id}`, { quantity }, { preserveScroll: true });
const remove = (id) => router.delete(`/cart/items/${id}`, { preserveScroll: true });
const applyCoupon = () => router.post('/cart/coupon', { code: code.value }, { preserveScroll: true });
</script>

<template>
    <Head :title="t('cart.title')" />
    <div class="container-shop max-w-3xl py-6 pb-28">
        <h1 class="type-h1">{{ t('cart.title') }}</h1>

        <div v-if="!cart.items?.length" class="mt-10 rounded-[var(--r-xl)] bg-[var(--surface)] px-6 py-12 text-center shadow-[var(--sh-sm)]">
            <img src="/images/mascots.svg" alt="" class="mx-auto h-36 w-36 object-contain" />
            <p class="mt-4 font-heading text-xl font-extrabold">{{ t('cart.empty', 'سلتك لسه فاضية.. تعالى نملاها حلويات 🍩') }}</p>
            <Link href="/shop" class="btn-primary mt-6 inline-flex">{{ t('home.cta_see_menu') }}</Link>
        </div>

        <div v-else class="mt-6 space-y-4">
            <article
                v-for="item in cart.items"
                :key="item.id"
                class="flex gap-3 rounded-[var(--r-lg)] bg-[var(--surface)] p-3 shadow-[var(--sh-sm)]"
            >
                <img
                    :src="item.image || '/images/mascots.svg'"
                    class="h-[110px] w-[88px] shrink-0 rounded-[16px] object-cover bg-[var(--pink-100)]"
                    alt=""
                />
                <div class="flex min-w-0 flex-1 flex-col">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="truncate font-bold text-[var(--cocoa-900)]">{{ item.name }}</div>
                            <div v-if="item.variant" class="mt-1 inline-flex rounded-full bg-[var(--pink-50)] px-2 py-0.5 text-[11px] font-semibold text-[var(--pink-700)]">
                                {{ item.variant }}
                            </div>
                        </div>
                        <button type="button" class="btn-ghost !h-10 !w-10 text-[var(--color-danger)]" :aria-label="t('common.delete')" @click="remove(item.id)">
                            <Trash2 class="h-4 w-4" :stroke-width="1.75" />
                        </button>
                    </div>
                    <div class="mt-auto flex items-center justify-between gap-2 pt-3">
                        <div class="qty-stepper !h-10">
                            <button type="button" @click="updateQty(item.id, item.quantity-1)">−</button>
                            <span>{{ item.quantity }}</span>
                            <button type="button" @click="updateQty(item.id, item.quantity+1)">+</button>
                        </div>
                        <div class="dir-ltr type-price">{{ item.line_total }}</div>
                    </div>
                </div>
            </article>

            <div class="rounded-[var(--r-lg)] bg-[var(--surface)] p-4 shadow-[var(--sh-sm)] space-y-4">
                <div class="flex h-12 overflow-hidden rounded-full border border-[var(--cocoa-100)] bg-[var(--cream)]">
                    <input
                        v-model="code"
                        class="min-w-0 flex-1 bg-transparent px-4 text-sm outline-none"
                        :placeholder="t('cart.coupon')"
                    />
                    <button class="px-5 text-sm font-bold text-[var(--pink-700)]" @click="applyCoupon">
                        {{ t('cart.apply_coupon', 'تطبيق') }}
                    </button>
                </div>
                <div class="flex justify-between text-sm"><span class="text-[var(--cocoa-500)]">{{ t('cart.subtotal') }}</span><span class="dir-ltr font-semibold">{{ cart.summary.subtotal }} ج.م</span></div>
                <div class="flex justify-between text-sm"><span class="text-[var(--cocoa-500)]">{{ t('cart.discount') }}</span><span class="dir-ltr font-semibold">- {{ cart.summary.discount }} ج.م</span></div>
                <div class="flex justify-between text-sm"><span class="text-[var(--cocoa-500)]">{{ t('cart.delivery') }}</span><span>{{ t('cart.delivery_by_area') }}</span></div>
                <div v-if="cart.summary.farthest_availability && cart.summary.farthest_availability !== 'available_now'" class="alert alert--warning">
                    {{ t('cart.farthest_delivery') }}: {{ cart.summary.farthest_availability }}
                </div>
                <div class="flex justify-between border-t border-[var(--cocoa-100)] pt-3 text-lg font-bold">
                    <span>{{ t('cart.total') }}</span>
                    <span class="dir-ltr text-[var(--pink-700)]">{{ cart.summary.total }} ج.م</span>
                </div>
                <Link href="/checkout" class="btn-primary block text-center">{{ t('layout.cart_continue', 'كمّل الطلب') }}</Link>
            </div>
        </div>
    </div>
</template>
