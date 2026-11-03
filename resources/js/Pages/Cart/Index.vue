<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
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
    <div class="mx-auto max-w-3xl px-4 py-6">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('cart.title') }}</h1>

        <div v-if="!cart.items?.length" class="mt-10 rounded-3xl bg-white p-10 text-center">
            <p>{{ t('cart.empty') }}</p>
            <Link href="/shop" class="btn-primary mt-4 inline-flex">{{ t('home.cta_see_menu') }}</Link>
        </div>

        <div v-else class="mt-6 space-y-4">
            <article v-for="item in cart.items" :key="item.id" class="flex gap-3 rounded-3xl bg-white p-3 shadow-sm">
                <img :src="item.image || '/images/logo-placeholder.svg'" class="h-24 w-24 rounded-2xl object-cover" />
                <div class="flex-1">
                    <div class="font-semibold">{{ item.name }}</div>
                    <div v-if="item.variant" class="text-xs text-[var(--color-text-muted)]">{{ item.variant }}</div>
                    <div class="mt-2 flex items-center gap-2">
                        <button class="rounded-full bg-[var(--color-denim-soft)] px-2" @click="updateQty(item.id, item.quantity-1)">−</button>
                        <span>{{ item.quantity }}</span>
                        <button class="rounded-full bg-[var(--color-denim-soft)] px-2" @click="updateQty(item.id, item.quantity+1)">+</button>
                        <button class="ms-auto text-xs text-red-500" @click="remove(item.id)">{{ t('common.delete') }}</button>
                    </div>
                </div>
                <div class="font-bold text-[var(--color-primary-dark)]">{{ item.line_total }}</div>
            </article>

            <div class="rounded-3xl bg-white p-4 shadow-sm space-y-3">
                <div class="flex gap-2">
                    <input v-model="code" class="flex-1 rounded-full border px-4 py-2 text-sm" :placeholder="t('cart.coupon')" />
                    <button class="btn-secondary !py-2" @click="applyCoupon">{{ t('cart.apply_coupon') }}</button>
                </div>
                <div class="flex justify-between text-sm"><span>{{ t('cart.subtotal') }}</span><span>{{ cart.summary.subtotal }} ج.م</span></div>
                <div class="flex justify-between text-sm"><span>{{ t('cart.discount') }}</span><span>- {{ cart.summary.discount }} ج.م</span></div>
                <div class="flex justify-between text-sm"><span>{{ t('cart.delivery') }}</span><span>{{ t('cart.delivery_by_area') }}</span></div>
                <div v-if="cart.summary.farthest_availability && cart.summary.farthest_availability !== 'available_now'" class="rounded-2xl bg-[var(--color-primary-soft)] p-3 text-xs">
                    {{ t('cart.farthest_delivery') }}: {{ cart.summary.farthest_availability }}
                </div>
                <div class="flex justify-between font-bold text-lg"><span>{{ t('cart.total') }}</span><span>{{ cart.summary.total }} ج.م</span></div>
                <Link href="/checkout" class="btn-primary block text-center">{{ t('layout.cart_continue') }}</Link>
            </div>
        </div>
    </div>
</template>
