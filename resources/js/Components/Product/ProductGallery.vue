<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    media: { type: Array, default: () => [] },
    alt: { type: String, default: '' },
});

const items = computed(() => {
    const list = (props.media || []).filter((m) => m?.path);
    if (list.length) return list;
    return [{ path: '/images/mascots.svg', type: 'image', alt_ar: props.alt }];
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
        <div class="relative overflow-hidden rounded-[var(--r-lg)] bg-[var(--pink-100)]">
            <video
                v-if="isVideo"
                :key="current.path"
                class="aspect-[4/5] w-full object-cover"
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
                class="aspect-[4/5] w-full object-cover"
                loading="eager"
                decoding="async"
                fetchpriority="high"
            />
            <span
                v-if="isVideo"
                class="absolute start-3 top-3 rounded-full bg-[var(--cocoa-900)]/55 px-2.5 py-1 text-[11px] font-semibold text-white"
            >
                فيديو
            </span>
            <div
                v-if="items.length > 1"
                class="absolute inset-x-0 bottom-3 flex justify-center gap-1.5"
            >
                <button
                    v-for="(_, index) in items"
                    :key="index"
                    type="button"
                    class="h-2 rounded-full transition-all"
                    :class="active === index ? 'w-5 bg-[var(--pink-600)]' : 'w-2 bg-white/80'"
                    :aria-label="`صورة ${index + 1}`"
                    @click="active = index"
                />
            </div>
        </div>

        <div v-if="items.length > 1" class="hidden h-scroll md:flex">
            <button
                v-for="(item, index) in items"
                :key="`${item.path}-${index}`"
                type="button"
                class="relative h-20 w-16 shrink-0 overflow-hidden rounded-[12px] ring-2 transition"
                :class="active === index ? 'ring-[var(--pink-500)]' : 'ring-transparent opacity-80 hover:opacity-100'"
                :aria-label="item.type === 'video' ? 'عرض الفيديو' : `صورة ${index + 1}`"
                @click="active = index"
            >
                <img
                    :src="item.type === 'video' ? (item.poster_path || item.path) : item.path"
                    :alt="item.alt_ar || alt"
                    class="h-full w-full object-cover"
                    loading="lazy"
                />
            </button>
        </div>
    </div>
</template>
