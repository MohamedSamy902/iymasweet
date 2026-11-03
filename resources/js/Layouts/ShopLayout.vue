<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Home, ShoppingBag, ShoppingCart, UserRound, ClipboardList, Search, Bell } from '@lucide/vue';
import { useUiText } from '@/Composables/useUiText';
import ChatWidget from '@/Components/Chatbot/ChatWidget.vue';
import WelcomePopup from '@/Components/WelcomePopup.vue';

const page = usePage();
const { t } = useUiText();
const scrolled = ref(false);

const onScroll = () => {
    scrolled.value = window.scrollY > 8;
};

onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});
onUnmounted(() => window.removeEventListener('scroll', onScroll));

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

        <header
            class="sticky top-0 z-40 bg-[var(--cream)]/90 backdrop-blur-md transition-[box-shadow,border-color] duration-200"
            :class="scrolled ? 'border-b border-[var(--cocoa-100)] shadow-[var(--sh-sm)]' : 'border-b border-transparent'"
        >
            <div class="container-shop flex h-[60px] items-center justify-between gap-3 lg:h-[76px]">
                <Link href="/" class="flex min-w-0 items-center gap-2.5">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[var(--pink-100)] lg:h-12 lg:w-12"
                    >
                        <img
                            :src="brand.logo || '/images/products/donut-1.jpg'"
                            alt=""
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div class="min-w-0 leading-tight">
                        <div class="truncate font-heading text-[20px] font-extrabold text-[var(--cocoa-900)] lg:text-[24px]">
                            {{ brand.name || 'Iyma Sweet' }}
                        </div>
                        <div class="hidden truncate text-[12px] font-semibold text-[var(--cocoa-500)] sm:block">
                            {{ brand.tagline || 'Handmade with love' }}
                        </div>
                    </div>
                </Link>

                <nav class="hidden flex-1 items-center justify-center gap-8 md:flex" aria-label="التنقل الرئيسي">
                    <Link href="/" class="text-sm font-semibold text-[var(--cocoa-700)] hover:text-[var(--pink-700)]">
                        {{ t('layout.nav_home', 'الرئيسية') }}
                    </Link>
                    <Link href="/shop" class="text-sm font-semibold text-[var(--cocoa-700)] hover:text-[var(--pink-700)]">
                        {{ t('layout.nav_shop', 'المنيو') }}
                    </Link>
                    <Link href="/bundles" class="text-sm font-semibold text-[var(--cocoa-700)] hover:text-[var(--pink-700)]">
                        {{ t('layout.nav_bundles', 'الباكدجات') }}
                    </Link>
                    <Link href="/account/orders" class="text-sm font-semibold text-[var(--cocoa-700)] hover:text-[var(--pink-700)]">
                        {{ t('layout.nav_orders', 'طلباتي') }}
                    </Link>
                </nav>

                <div class="flex shrink-0 items-center gap-1.5">
                    <Link
                        href="/shop"
                        class="btn-ghost md:hidden"
                        :aria-label="t('layout.nav_search', 'بحث')"
                    >
                        <Search class="h-[22px] w-[22px]" :stroke-width="1.75" />
                    </Link>
                    <Link
                        :href="`/locale/${locale === 'ar' ? 'en' : 'ar'}`"
                        class="hidden rounded-full border border-[var(--cocoa-100)] bg-[var(--surface)] px-3 py-2 text-xs font-bold text-[var(--denim-700)] sm:inline-flex"
                    >
                        {{ locale === 'ar' ? 'EN' : 'عر' }}
                    </Link>
                    <Link
                        href="/cart"
                        class="btn-ghost relative"
                        :aria-label="t('layout.nav_cart', 'السلة')"
                    >
                        <ShoppingCart class="h-[22px] w-[22px]" :stroke-width="1.75" />
                        <span
                            v-if="cartCount > 0"
                            class="absolute end-0.5 top-0.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[var(--pink-600)] px-1 text-[10px] font-bold text-white"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>
                    <span class="btn-ghost relative hidden text-[var(--cocoa-500)] lg:inline-flex" aria-hidden="true">
                        <Bell class="h-[22px] w-[22px]" :stroke-width="1.75" />
                    </span>
                </div>
            </div>
        </header>

        <main id="main" class="w-full max-w-full overflow-x-clip">
            <slot />
        </main>

        <footer class="mt-16 bg-[var(--cocoa-900)] text-white">
            <div class="footer-rainbow" aria-hidden="true" />
            <div class="container-shop grid gap-10 py-12 md:grid-cols-[1.4fr_1fr_1fr]">
                <div>
                    <div class="font-heading text-2xl font-extrabold">
                        {{ brand.name || 'Iyma Sweet' }}
                    </div>
                    <p class="mt-3 max-w-md text-sm leading-relaxed text-white/75">
                        {{ t('layout.footer_blurb', 'حلويات منزلية طازجة، توصيل لباب البيت.') }}
                    </p>
                    <p class="mt-3 text-xs font-semibold text-[var(--pink-300)]">
                        {{ brand.tagline || 'Handmade with love' }}
                    </p>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="font-bold">{{ t('layout.footer_links', 'روابط سريعة') }}</div>
                    <Link href="/shop" class="block text-white/70 hover:text-white">{{ t('layout.nav_shop', 'المنيو') }}</Link>
                    <Link href="/bundles" class="block text-white/70 hover:text-white">{{ t('layout.nav_bundles', 'البوكسات') }}</Link>
                    <Link href="/faq" class="block text-white/70 hover:text-white">{{ t('pages.faq', 'أسئلة شائعة') }}</Link>
                    <Link href="/contact" class="block text-white/70 hover:text-white">{{ t('pages.contact', 'تواصل معنا') }}</Link>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="font-bold">{{ t('layout.footer_contact', 'تواصل') }}</div>
                    <a :href="whatsappUrl" target="_blank" rel="noopener" class="block text-white/70 hover:text-white">
                        واتساب
                        <span class="dir-ltr">{{ brand.phone || '01007366484' }}</span>
                    </a>
                    <a v-if="brand.instagram" :href="brand.instagram" target="_blank" rel="noopener" class="block text-white/70 hover:text-white">إنستجرام</a>
                    <a v-if="brand.facebook" :href="brand.facebook" target="_blank" rel="noopener" class="block text-white/70 hover:text-white">فيسبوك</a>
                    <p class="pt-1 text-xs text-white/55">{{ brand.address || 'القاهرة، مصر' }}</p>
                    <Link href="/shipping" class="block text-white/70 hover:text-white">{{ t('pages.shipping', 'الدفع والتوصيل') }}</Link>
                    <Link href="/terms" class="block text-white/70 hover:text-white">{{ t('pages.terms', 'الشروط') }}</Link>
                </div>
            </div>
            <div class="border-t border-white/10">
                <p class="container-shop py-4 text-xs text-white/50">
                    © {{ new Date().getFullYear() }} {{ brand.name || 'Iyma Sweet' }} ·
                    {{ t('layout.footer_rights', 'جميع الحقوق محفوظة') }}
                </p>
            </div>
        </footer>

        <WelcomePopup />
        <ChatWidget v-if="chatbotEnabled" />
    </div>

    <Teleport to="body">
        <div
            v-if="cartCount > 0"
            class="iyma-mini-cart fixed z-[60] animate-slide-up md:hidden"
        >
            <Link
                href="/checkout"
                class="flex h-14 items-center justify-between gap-3 rounded-full bg-[var(--cocoa-900)] px-4 text-sm font-semibold text-white shadow-[var(--sh-lg)]"
            >
                <span class="truncate">
                    {{ t('layout.items_count', '{count} منتجات').replace('{count}', String(cartCount)) }}
                </span>
                <span class="inline-flex h-10 items-center rounded-full bg-[var(--pink-600)] px-4 text-[13px] font-bold">
                    {{ t('layout.cart_continue', 'كمّل الطلب') }}
                </span>
            </Link>
        </div>

        <a
            v-if="!chatbotEnabled"
            :href="whatsappUrl"
            target="_blank"
            rel="noopener"
            class="iyma-wa-fab wa-fab-pulse relative fixed z-[70] flex h-14 w-14 items-center justify-center rounded-full text-white shadow-[var(--sh-lg)]"
            style="background: linear-gradient(145deg, var(--pink-500), var(--pink-600))"
            aria-label="واتساب Iyma Sweet"
            title="واتساب"
        >
            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="currentColor" aria-hidden="true">
                <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.9 11.9 0 0 0 5.75 1.47h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.44-8.44ZM12.06 21.15h-.01a9.24 9.24 0 0 1-4.71-1.29l-.34-.2-3.74.98 1-3.64-.22-.37a9.23 9.23 0 0 1-1.41-4.92c0-5.1 4.15-9.25 9.26-9.25a9.2 9.2 0 0 1 6.54 2.71 9.2 9.2 0 0 1 2.71 6.55c0 5.1-4.15 9.25-9.24 9.25Zm5.37-6.93c-.29-.15-1.73-.85-2-.95-.27-.1-.46-.15-.66.15-.2.29-.76.95-.93 1.15-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.44-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.44-.51.15-.17.2-.29.29-.49.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.58-.48-.5-.66-.51h-.56c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.73-.71 1.97-1.39.24-.68.24-1.27.17-1.39-.07-.12-.26-.2-.55-.34Z" />
            </svg>
        </a>

        <nav
            class="iyma-bottom-nav fixed inset-x-0 bottom-0 z-[80] border-t border-[var(--cocoa-100)] bg-[var(--surface)] pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_24px_rgba(201,71,122,0.10)] md:hidden"
            aria-label="التنقل السفلي"
        >
            <ul class="mx-auto grid h-16 max-w-lg grid-cols-5">
                <li v-for="item in navItems" :key="item.href" class="min-w-0">
                    <Link
                        :href="item.href"
                        class="relative flex h-full flex-col items-center justify-center gap-0.5 px-0.5 text-[11px] leading-tight"
                        :class="isActive(item.href) ? 'font-bold text-[var(--pink-600)]' : 'font-semibold text-[var(--cocoa-500)]'"
                    >
                        <span
                            class="relative flex h-8 w-10 items-center justify-center rounded-full"
                            :class="isActive(item.href) ? 'bg-[var(--pink-100)]' : ''"
                        >
                            <component :is="item.icon" class="h-5 w-5 shrink-0" :stroke-width="1.75" />
                            <span
                                v-if="item.badge"
                                class="absolute -end-0.5 -top-0.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[var(--pink-600)] px-1 text-[10px] font-bold text-white"
                            >
                                {{ item.badge }}
                            </span>
                        </span>
                        <span class="max-w-full truncate px-0.5">{{ item.label }}</span>
                    </Link>
                </li>
            </ul>
        </nav>
    </Teleport>
</template>

<style scoped>
.iyma-mini-cart {
    inset-inline-start: 16px;
    inset-inline-end: 16px;
    width: calc(100% - 32px);
    max-width: 420px;
    margin-inline: auto;
    bottom: calc(4.75rem + 12px + env(safe-area-inset-bottom, 0px));
}

.iyma-wa-fab {
    inset-inline-end: max(16px, env(safe-area-inset-inline-end, 0px));
    bottom: calc(5.5rem + env(safe-area-inset-bottom, 0px));
}

@media (min-width: 768px) {
    .iyma-wa-fab {
        bottom: 24px;
    }
}
</style>
