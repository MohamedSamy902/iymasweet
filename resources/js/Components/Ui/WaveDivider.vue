<script setup>
defineProps({
    color: { type: String, default: 'var(--surface-tint)' },
});
</script>

<template>
    <div class="wave-divider" :style="{ color }" aria-hidden="true">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0 40C120 80 240 0 360 40S600 80 720 40 960 0 1080 40 1320 80 1440 40V80H0Z" fill="currentColor" />
        </svg>
    </div>
</template>
