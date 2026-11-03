<script setup>
import { computed, ref } from 'vue';
import Sparkle from '@/Components/Ui/Sparkle.vue';

const props = defineProps({
    src: { type: String, default: '' },
    alt: { type: String, default: '' },
    category: { type: String, default: 'donut' }, // donut | cake | cookie | box
    soldOut: { type: Boolean, default: false },
    eager: { type: Boolean, default: false },
});

const failed = ref(false);
const showFallback = computed(() => !props.src || failed.value || props.src.includes('logo-placeholder'));

const tint = computed(() => {
    const map = {
        donut: 'var(--pink-100)',
        cake: 'var(--denim-100)',
        cookie: '#FFF0D6',
        box: '#EFE9FB',
        tort: 'var(--denim-100)',
        default: 'var(--pink-100)',
    };
    return map[props.category] || map.default;
});

const iconPath = computed(() => {
    // Simple stroke icons matching Lucide style
    if (props.category === 'cake' || props.category === 'tort') {
        return 'M8 14h16v8a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-8Zm2-4c0-4 3-7 6-7s6 3 6 7M12 7c1-2 3-3 4-2';
    }
    if (props.category === 'cookie') {
        return 'M12 3a9 9 0 1 0 9 10 4 4 0 0 1-5-5 4 4 0 0 1-4-5Zm-2 8h.01M15 11h.01M10 15h.01';
    }
    if (props.category === 'box') {
        return 'M3 8l9-5 9 5v9l-9 5-9-5V8Zm9 5v9M3 8l9 5 9-5';
    }
    // donut
    return 'M12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z';
});
</script>

<template>
    <div class="product-card__media" :class="{ 'is-sold': soldOut }" :style="{ background: tint }">
        <img
            v-if="!showFallback"
            :src="src"
            :alt="alt"
            :loading="eager ? 'eager' : 'lazy'"
            decoding="async"
            @error="failed = true"
        />
        <div v-else class="flex h-full w-full flex-col items-center justify-center gap-3 p-4" aria-hidden="true">
            <svg viewBox="0 0 32 32" class="h-14 w-14 text-[var(--pink-400)]" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path :d="iconPath" />
            </svg>
            <div class="flex gap-2">
                <Sparkle :size="12" />
                <Sparkle :size="16" tone="sun" />
                <Sparkle :size="12" />
            </div>
        </div>
        <div v-if="soldOut" class="product-card__sold-ribbon">خلص</div>
    </div>
</template>
