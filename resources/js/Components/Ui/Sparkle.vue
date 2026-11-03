<script setup>
defineProps({
    size: { type: [Number, String], default: 16 },
    tone: { type: String, default: 'pink' }, // pink | sun
});
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="currentColor"
        aria-hidden="true"
        :class="tone === 'sun' ? 'text-[var(--sun)]' : 'text-[var(--pink-300)]'"
    >
        <path d="M12 0C12.9 7.2 16.8 11.1 24 12C16.8 12.9 12.9 16.8 12 24C11.1 16.8 7.2 12.9 0 12C7.2 11.1 11.1 7.2 12 0Z" />
    </svg>
</template>
