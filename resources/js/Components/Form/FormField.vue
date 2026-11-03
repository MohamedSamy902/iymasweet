<script setup>
defineProps({
    label: { type: String, required: true },
    required: { type: Boolean, default: false },
    optional: { type: Boolean, default: false },
    hint: { type: String, default: '' },
    error: { type: String, default: '' },
    forId: { type: String, default: '' },
});
</script>

<template>
    <div class="field" :class="{ 'field--error': !!error }">
        <label v-if="label" class="field-label" :for="forId || undefined">
            <span>{{ label }}</span>
            <span v-if="required" class="field-required" title="مطلوب">*</span>
            <span v-else-if="optional" class="field-optional">اختياري</span>
        </label>
        <div class="field-control">
            <slot />
        </div>
        <p v-if="error" class="field-error" role="alert">{{ error }}</p>
        <p v-else-if="hint" class="field-hint">{{ hint }}</p>
    </div>
</template>
