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
    <div class="iyma-shell min-h-dvh pb-[calc(5.5rem+env(safe-area-inset-bottom))] md:pb-0">
        <div
            v-if="announcement.enabled && announcement.text"
            class="bg-[var(--color-primary)] px-4 py-2 text-center text-sm font-semibold text-white"
        >
            {{ announcement.text }}
        </div>

        <header class="sticky top-0 z-40 border-b border-[var(--color-primary-soft)]/70 bg-[var(--color-cream)]/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3">
                <Link href="/" class="flex min-w-0 items-center gap-2">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[var(--color-primary-soft)] shadow-sm"
                    >
                        <img src="/images/products/donut-1.jpg" alt="" class="h-full w-full object-cover" />
                    </div>
                    <div class="min-w-0 leading-tight">
                        <div class="truncate font-heading text-xl text-[var(--color-brown)]">
                            {{ brand.name || 'Iyma Sweet' }}
                        </div>
                        <div class="truncate text-xs text-[var(--color-text-muted)]">
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

                <div class="flex shrink-0 items-center gap-2">
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

        <footer class="mt-16 border-t border-[var(--color-primary-soft)] bg-white/85">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 md:grid-cols-[1.4fr_1fr_1fr]">
                <div>
                    <div class="font-heading text-2xl text-[var(--color-brown)]">
                        {{ brand.name || 'Iyma Sweet' }}
                    </div>
                    <p class="mt-2 max-w-md text-sm leading-relaxed text-[var(--color-text)]">
                        {{ t('layout.footer_blurb', 'حلويات منزلية طازجة، توصيل لباب البيت.') }}
                    </p>
                    <p class="mt-2 text-xs font-semibold text-[var(--color-primary-dark)]">
                        {{ brand.tagline || 'Handmade with love' }}
                    </p>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="font-semibold text-[var(--color-brown)]">روابط سريعة</div>
                    <Link href="/shop" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">{{ t('layout.nav_shop', 'المنيو') }}</Link>
                    <Link href="/bundles" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">{{ t('layout.nav_bundles', 'البوكسات') }}</Link>
                    <Link href="/faq" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">أسئلة شائعة</Link>
                    <Link href="/contact" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">تواصل معنا</Link>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="font-semibold text-[var(--color-brown)]">سياسات</div>
                    <Link href="/shipping" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">الدفع والتوصيل</Link>
                    <Link href="/refund" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">الإلغاء والاسترجاع</Link>
                    <Link href="/privacy" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">الخصوصية</Link>
                    <Link href="/terms" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">الشروط</Link>
                </div>
            </div>
            <div class="border-t border-black/5">
                <p class="mx-auto max-w-6xl px-4 py-4 text-xs text-[var(--color-text-muted)]">
                    © {{ new Date().getFullYear() }} {{ brand.name || 'Iyma Sweet' }} ·
                    {{ t('layout.footer_rights', 'جميع الحقوق محفوظة') }}
                </p>
            </div>
        </footer>

        <!-- Mini cart: leave gap for chat FAB on the end side -->
        <div
            v-if="cartCount > 0"
            class="iyma-mini-cart fixed z-40 md:hidden"
        >
            <Link
                href="/checkout"
                class="flex items-center justify-between gap-3 rounded-2xl bg-[var(--color-brown)] px-4 py-3 text-sm font-semibold text-white shadow-lg"
            >
                <span>{{ t('layout.items_count', '{count} منتجات').replace('{count}', String(cartCount)) }}</span>
                <span>{{ t('layout.cart_continue', 'إتمام الطلب') }}</span>
            </Link>
        </div>

        <ChatWidget />

        <nav
            class="iyma-bottom-nav fixed inset-x-0 bottom-0 z-50 border-t border-[var(--color-primary-soft)] bg-white/95 pb-[env(safe-area-inset-bottom)] backdrop-blur md:hidden"
            aria-label="Bottom navigation"
        >
            <ul class="grid grid-cols-5">
                <li v-for="item in navItems" :key="item.href">
                    <Link
                        :href="item.href"
                        class="relative flex flex-col items-center gap-1 px-1 py-2 text-[11px] font-semibold text-[var(--color-text-muted)]"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                        <span class="truncate">{{ item.label }}</span>
                        <span
                            v-if="item.badge"
                            class="absolute top-1 end-2 flex h-4 min-w-4 items-center justify-center rounded-full bg-[var(--color-primary)] px-1 text-[10px] text-white"
                        >
                            {{ item.badge }}
                        </span>
                    </Link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>
.iyma-mini-cart {
    inset-inline-start: 0.75rem;
    inset-inline-end: 5rem; /* room for chat FAB */
    bottom: calc(4.35rem + env(safe-area-inset-bottom, 0px));
}

:global(html.iyma-chat-open) .iyma-mini-cart,
:global(html.iyma-chat-open) .iyma-bottom-nav {
    visibility: hidden;
    pointer-events: none;
}
</style>
