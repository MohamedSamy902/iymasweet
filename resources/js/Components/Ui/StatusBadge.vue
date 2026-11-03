<script setup>
import { Clock3, CalendarDays } from '@lucide/vue';

defineProps({
    type: { type: String, default: 'available_now' },
    label: { type: String, required: true },
});
</script>

<template>
    <span class="badge-status" :class="`badge-${type}`">
        <span v-if="type === 'available_now'" class="badge-dot" aria-hidden="true" />
        <Clock3 v-else-if="type === 'available_tomorrow'" class="h-3 w-3" :stroke-width="2" aria-hidden="true" />
        <CalendarDays v-else-if="type === 'pre_order'" class="h-3 w-3" :stroke-width="2" aria-hidden="true" />
        {{ label }}
    </span>
</template>
