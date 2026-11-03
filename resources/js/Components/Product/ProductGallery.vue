<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    media: { type: Array, default: () => [] },
    alt: { type: String, default: '' },
});

const items = computed(() => {
    const list = (props.media || []).filter((m) => m?.path);
    if (list.length) return list;
    return [{ path: '/images/logo-placeholder.svg', type: 'image', alt_ar: props.alt }];
});

const active = ref(0);

watch(items, () => {
    active.value = 0;
});

const current = computed(() => items.value[active.value] || items.value[0]);
const isVideo = computed(() => (current.value?.type || 'image') === 'video');
</script>

<template>
    <div class="product-gallery space-y-3">
        <div class="relative overflow-hidden rounded-2xl bg-[var(--color-primary-soft)]/40 ring-1 ring-black/5">
            <video
                v-if="isVideo"
                :key="current.path"
                class="aspect-square w-full object-cover"
                controls
                playsinline
                preload="metadata"
                :poster="current.poster_path || undefined"
            >
                <source :src="current.path" :type="current.mime || 'video/mp4'" />
            </video>
            <img
                v-else
                :src="current.path"
                :alt="current.alt_ar || alt"
                class="aspect-square w-full bg-[#f6e9ee] object-cover transition duration-500"
                loading="eager"
                decoding="async"
            />
            <span
                v-if="isVideo"
                class="absolute start-3 top-3 rounded-full bg-black/55 px-2.5 py-1 text-[11px] font-semibold text-white"
            >
                فيديو
            </span>
        </div>

        <div v-if="items.length > 1" class="h-scroll">
            <button
                v-for="(item, index) in items"
                :key="`${item.path}-${index}`"
                type="button"
                class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl ring-2 transition"
                :class="active === index ? 'ring-[var(--color-primary)]' : 'ring-transparent opacity-80 hover:opacity-100'"
                :aria-label="item.type === 'video' ? 'عرض الفيديو' : `صورة ${index + 1}`"
                @click="active = index"
            >
                <img
                    :src="item.type === 'video' ? (item.poster_path || item.path) : item.path"
                    :alt="item.alt_ar || alt"
                    class="h-full w-full object-cover"
                    loading="lazy"
                />
                <span
                    v-if="item.type === 'video'"
                    class="absolute inset-0 flex items-center justify-center bg-black/35 text-xs font-bold text-white"
                >
                    ▶
                </span>
            </button>
        </div>
    </div>
</template>
