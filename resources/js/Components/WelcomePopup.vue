<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const open = ref(false);

const popup = computed(() => page.props.welcomePopup || null);

const storageKey = computed(() => {
    if (!popup.value?.id) return 'iyma_welcome_seen';
    return `iyma_welcome_seen_${popup.value.id}_${popup.value.version || ''}`;
});

const image = computed(() => popup.value?.image_mobile || popup.value?.image_desktop || '');
const isExternal = computed(() => /^https?:\/\//i.test(String(popup.value?.link || '')));

const close = () => {
    open.value = false;
    try {
        localStorage.setItem(storageKey.value, '1');
    } catch {
        // ignore
    }
    document.documentElement.classList.remove('iyma-popup-open');
    document.body.style.overflow = '';
};

const openPopup = () => {
    open.value = true;
    document.documentElement.classList.add('iyma-popup-open');
    document.body.style.overflow = 'hidden';
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && open.value) close();
};

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
    if (!popup.value?.enabled) return;
    try {
        if (localStorage.getItem(storageKey.value) === '1') return;
    } catch {
        // show anyway
    }
    window.setTimeout(openPopup, 350);
});

watch(open, (value) => {
    if (!value) {
        document.documentElement.classList.remove('iyma-popup-open');
        document.body.style.overflow = '';
    }
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    document.documentElement.classList.remove('iyma-popup-open');
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open && popup"
            class="iyma-welcome-root fixed inset-0 z-[100] flex items-end justify-center p-0 sm:items-center sm:p-4"
        >
            <button type="button" class="absolute inset-0 bg-black/45" aria-label="إغلاق" @click="close" />

            <section
                role="dialog"
                aria-modal="true"
                :aria-label="popup.title || 'ترحيب'"
                class="relative z-10 w-full max-w-md overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-3xl"
            >
                <button
                    type="button"
                    class="absolute end-3 top-3 z-20 flex h-9 w-9 items-center justify-center rounded-full bg-black/40 text-lg text-white"
                    aria-label="إغلاق"
                    @click="close"
                >
                    ×
                </button>

                <div v-if="image" class="aspect-[16/10] overflow-hidden bg-[var(--color-primary-soft)]">
                    <img :src="image" :alt="popup.title || ''" class="h-full w-full object-cover" />
                </div>

                <div class="space-y-3 p-5 text-center">
                    <h2 class="font-heading text-2xl text-[var(--color-brown)]">{{ popup.title }}</h2>
                    <p v-if="popup.subtitle" class="text-sm leading-relaxed text-[var(--color-text)]">{{ popup.subtitle }}</p>

                    <div class="flex flex-col gap-2 pt-1">
                        <a
                            v-if="popup.cta && popup.link && isExternal"
                            :href="popup.link"
                            class="btn-primary w-full"
                            target="_blank"
                            rel="noopener"
                            @click="close"
                        >
                            {{ popup.cta }}
                        </a>
                        <Link
                            v-else-if="popup.cta && popup.link"
                            :href="popup.link"
                            class="btn-primary w-full"
                            @click="close"
                        >
                            {{ popup.cta }}
                        </Link>
                        <button type="button" class="btn-secondary w-full" @click="close">
                            تصفح المتجر
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </Teleport>
</template>
