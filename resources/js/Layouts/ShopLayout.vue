<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, ShoppingBag, ShoppingCart, UserRound, ClipboardList } from '@lucide/vue';
import { useUiText } from '@/Composables/useUiText';
import ChatWidget from '@/Components/Chatbot/ChatWidget.vue';

const page = usePage();
const { t } = useUiText();

const settings = computed(() => page.props.settings || {});
const brand = computed(() => settings.value.brand || {});
const announcement = computed(() => settings.value.announcement || {});
const showSparkles = computed(() => settings.value.features?.show_sparkles !== false);
const cartCount = computed(() => page.props.cartCount || 0);
const locale = computed(() => page.props.locale || 'ar');

const navItems = computed(() => [
    { href: '/', label: t('layout.nav_home', 'الرئيسية'), icon: Home },
    { href: '/shop', label: t('layout.nav_shop', 'المنيو'), icon: ShoppingBag },
    { href: '/cart', label: t('layout.nav_cart', 'السلة'), icon: ShoppingCart, badge: cartCount.value },
    { href: '/account/orders', label: t('layout.nav_orders', 'طلباتي'), icon: ClipboardList },
    { href: '/account', label: t('layout.nav_account', 'حسابي'), icon: UserRound },
]);
</script>

<template>
    <div class="min-h-dvh pb-24 md:pb-0">
        <div
            v-if="announcement.enabled && announcement.text"
            class="bg-[var(--color-primary)] px-4 py-2 text-center text-sm font-semibold text-white"
        >
            {{ announcement.text }}
        </div>

        <header class="sticky top-0 z-40 border-b border-[var(--color-primary-soft)]/70 bg-[var(--color-cream)]/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3">
                <Link href="/" class="flex items-center gap-2">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--color-primary-soft)] text-lg font-heading text-[var(--color-primary-dark)] shadow-sm animate-float"
                    >
                        ✦
                    </div>
                    <div class="leading-tight">
                        <div class="font-heading text-xl text-[var(--color-brown)]">
                            {{ brand.name || 'Iyma Sweet' }}
                            <span v-if="showSparkles" class="sparkle text-sm">✦</span>
                        </div>
                        <div class="text-xs text-[var(--color-text-muted)]">
                            {{ brand.tagline || 'Handmade with love' }}
                        </div>
                    </div>
                </Link>

                <div class="hidden flex-1 items-center justify-center gap-6 md:flex">
                    <Link href="/" class="text-sm font-semibold text-[var(--color-text)] hover:text-[var(--color-primary-dark)]">
                        {{ t('layout.nav_home', 'الرئيسية') }}
                    </Link>
                    <Link href="/shop" class="text-sm font-semibold text-[var(--color-text)] hover:text-[var(--color-primary-dark)]">
                        {{ t('layout.nav_shop', 'المنيو') }}
                    </Link>
                    <Link href="/bundles" class="text-sm font-semibold text-[var(--color-text)] hover:text-[var(--color-primary-dark)]">
                        {{ t('layout.nav_bundles', 'الباكدجات') }}
                    </Link>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        :href="`/locale/${locale === 'ar' ? 'en' : 'ar'}`"
                        class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-[var(--color-denim)] shadow-sm"
                    >
                        {{ locale === 'ar' ? 'EN' : 'عر' }}
                    </Link>
                    <Link
                        href="/cart"
                        class="relative rounded-full bg-white p-2.5 text-[var(--color-primary-dark)] shadow-sm"
                        :aria-label="t('layout.nav_cart', 'السلة')"
                    >
                        <ShoppingCart class="h-5 w-5" />
                        <span
                            v-if="cartCount > 0"
                            class="absolute -top-1 -end-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[var(--color-primary)] px-1 text-[10px] font-bold text-white"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer class="mt-16 border-t border-[var(--color-primary-soft)] bg-white/70">
            <div class="mx-auto max-w-6xl px-4 py-10">
                <div class="font-heading text-2xl text-[var(--color-brown)]">
                    {{ brand.name || 'Iyma Sweet' }}
                </div>
                <p class="mt-2 max-w-md text-sm text-[var(--color-text-muted)]">
                    {{ brand.tagline || 'Handmade with love' }}
                </p>
                <div class="rainbow-divider mt-6" />
                <p class="mt-4 text-xs text-[var(--color-text-muted)]">
                    © {{ new Date().getFullYear() }} {{ brand.name || 'Iyma Sweet' }} ·
                    {{ t('layout.footer_rights', 'جميع الحقوق محفوظة') }}
                </p>
            </div>
        </footer>

        <ChatWidget />

        <div
            v-if="cartCount > 0"
            class="fixed inset-x-3 bottom-[4.5rem] z-40 rounded-2xl bg-[var(--color-brown)] px-4 py-3 text-sm text-white shadow-lg md:hidden"
        >
            <Link href="/checkout" class="flex items-center justify-between font-semibold">
                <span>{{ cartCount }} منتجات</span>
                <span>{{ t('layout.cart_continue', 'كمّل الطلب') }}</span>
            </Link>
        </div>

        <!-- Mobile bottom nav -->
        <nav
            class="fixed inset-x-0 bottom-0 z-50 border-t border-[var(--color-primary-soft)] bg-white/95 pb-[env(safe-area-inset-bottom)] backdrop-blur md:hidden"
            aria-label="Bottom navigation"
        >
            <ul class="grid grid-cols-5">
                <li v-for="item in navItems" :key="item.href">
                    <Link
                        :href="item.href"
                        class="relative flex flex-col items-center gap-1 px-1 py-2 text-[11px] font-semibold text-[var(--color-text-muted)]"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                        <span>{{ item.label }}</span>
                        <span
                            v-if="item.badge"
                            class="absolute top-1 end-3 flex h-4 min-w-4 items-center justify-center rounded-full bg-[var(--color-primary)] px-1 text-[10px] text-white"
                        >
                            {{ item.badge }}
                        </span>
                    </Link>
                </li>
            </ul>
        </nav>
    </div>
</template>
