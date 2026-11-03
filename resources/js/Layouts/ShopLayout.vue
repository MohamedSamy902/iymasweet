<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, ShoppingBag, ShoppingCart, UserRound, ClipboardList } from '@lucide/vue';
import { useUiText } from '@/Composables/useUiText';
import ChatWidget from '@/Components/Chatbot/ChatWidget.vue';
import WelcomePopup from '@/Components/WelcomePopup.vue';

const page = usePage();
const { t } = useUiText();

const settings = computed(() => page.props.settings || {});
const brand = computed(() => settings.value.brand || {});
const announcement = computed(() => settings.value.announcement || {});
const features = computed(() => settings.value.features || {});
const chatbotEnabled = computed(() => !!features.value.chatbot_enabled);
const cartCount = computed(() => page.props.cartCount || 0);
const locale = computed(() => page.props.locale || 'ar');
const whatsappUrl = computed(() => {
    const raw = String(brand.value.whatsapp || '201007366484').replace(/\D/g, '');
    return `https://wa.me/${raw}`;
});

const navItems = computed(() => [
    { href: '/', label: t('layout.nav_home', 'الرئيسية'), icon: Home },
    { href: '/shop', label: t('layout.nav_shop', 'المنيو'), icon: ShoppingBag },
    { href: '/cart', label: t('layout.nav_cart', 'السلة'), icon: ShoppingCart, badge: cartCount.value },
    { href: '/account/orders', label: t('layout.nav_orders', 'طلباتي'), icon: ClipboardList },
    { href: '/account', label: t('layout.nav_account', 'حسابي'), icon: UserRound },
]);

const isActive = (href) => {
    const url = page.url || '/';
    if (href === '/') return url === '/' || url === '';
    return url === href || url.startsWith(`${href}/`) || url.startsWith(`${href}?`);
};
</script>

<template>
    <div class="iyma-shell min-h-dvh w-full pb-[calc(4.75rem+env(safe-area-inset-bottom))] md:pb-0">
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

        <main class="w-full max-w-full overflow-x-clip">
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
                    <div class="font-semibold text-[var(--color-brown)]">تواصل</div>
                    <a :href="whatsappUrl" target="_blank" rel="noopener" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">
                        واتساب {{ brand.phone || '01007366484' }}
                    </a>
                    <a v-if="brand.instagram" :href="brand.instagram" target="_blank" rel="noopener" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">إنستجرام</a>
                    <a v-if="brand.facebook" :href="brand.facebook" target="_blank" rel="noopener" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">فيسبوك</a>
                    <p class="pt-1 text-xs text-[var(--color-text-muted)]">{{ brand.address || 'القاهرة، مصر' }}</p>
                    <Link href="/shipping" class="block text-[var(--color-text-muted)] hover:text-[var(--color-denim)]">الدفع والتوصيل</Link>
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

        <WelcomePopup />
        <ChatWidget v-if="chatbotEnabled" />
    </div>

    <!-- Fixed chrome teleported outside overflow containers so it never clips/disappears -->
    <Teleport to="body">
        <div
            v-if="cartCount > 0"
            class="iyma-mini-cart fixed z-[60] md:hidden"
        >
            <Link
                href="/checkout"
                class="flex items-center justify-between gap-3 rounded-2xl bg-[var(--color-brown)] px-4 py-3 text-sm font-semibold text-white shadow-lg"
            >
                <span>{{ t('layout.items_count', '{count} منتجات').replace('{count}', String(cartCount)) }}</span>
                <span>{{ t('layout.cart_continue', 'إتمام الطلب') }}</span>
            </Link>
        </div>

        <a
            v-if="!chatbotEnabled"
            :href="whatsappUrl"
            target="_blank"
            rel="noopener"
            class="iyma-wa-fab fixed z-[70] flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg"
            aria-label="واتساب Iyma Sweet"
            title="واتساب"
        >
            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="currentColor" aria-hidden="true">
                <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.9 11.9 0 0 0 5.75 1.47h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.44-8.44ZM12.06 21.15h-.01a9.24 9.24 0 0 1-4.71-1.29l-.34-.2-3.74.98 1-3.64-.22-.37a9.23 9.23 0 0 1-1.41-4.92c0-5.1 4.15-9.25 9.26-9.25a9.2 9.2 0 0 1 6.54 2.71 9.2 9.2 0 0 1 2.71 6.55c0 5.1-4.15 9.25-9.24 9.25Zm5.37-6.93c-.29-.15-1.73-.85-2-.95-.27-.1-.46-.15-.66.15-.2.29-.76.95-.93 1.15-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.44-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.44-.51.15-.17.2-.29.29-.49.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.58-.48-.5-.66-.51h-.56c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.73-.71 1.97-1.39.24-.68.24-1.27.17-1.39-.07-.12-.26-.2-.55-.34Z" />
            </svg>
        </a>

        <nav
            class="iyma-bottom-nav fixed inset-x-0 bottom-0 z-[80] border-t border-[var(--color-primary-soft)] bg-white pb-[env(safe-area-inset-bottom)] shadow-[0_-6px_20px_rgba(122,62,38,0.08)] md:hidden"
            aria-label="التنقل السفلي"
        >
            <ul class="mx-auto grid max-w-lg grid-cols-5">
                <li v-for="item in navItems" :key="item.href" class="min-w-0">
                    <Link
                        :href="item.href"
                        class="relative flex h-14 flex-col items-center justify-center gap-0.5 px-0.5 text-[10px] font-semibold leading-tight"
                        :class="isActive(item.href) ? 'text-[var(--color-primary-dark)]' : 'text-[var(--color-text-muted)]'"
                    >
                        <component :is="item.icon" class="h-5 w-5 shrink-0" />
                        <span class="max-w-full truncate px-0.5">{{ item.label }}</span>
                        <span
                            v-if="item.badge"
                            class="absolute top-1 end-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-[var(--color-primary)] px-1 text-[10px] text-white"
                        >
                            {{ item.badge }}
                        </span>
                    </Link>
                </li>
            </ul>
        </nav>
    </Teleport>
</template>

<style scoped>
.iyma-mini-cart {
    inset-inline-start: 0.75rem;
    inset-inline-end: 5rem;
    bottom: calc(4.35rem + env(safe-area-inset-bottom, 0px));
}

.iyma-wa-fab {
    inset-inline-end: max(0.75rem, env(safe-area-inset-inline-end, 0px));
    bottom: calc(5.75rem + env(safe-area-inset-bottom, 0px));
}

@media (min-width: 768px) {
    .iyma-wa-fab {
        bottom: 1.5rem;
    }
}
</style>
