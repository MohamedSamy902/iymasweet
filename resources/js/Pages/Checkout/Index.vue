<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import FormField from '@/Components/Form/FormField.vue';
import { useUiText } from '@/Composables/useUiText';

defineOptions({ layout: ShopLayout });
const props = defineProps({
    cart: Object,
    guest_checkout: Boolean,
    governorates: Array,
    addresses: Array,
    payment_methods: Array,
    slots: Array,
    auth_user: Object,
});
const { t } = useUiText();
const step = ref(props.auth_user ? 2 : 1);

const form = useForm({
    guest_name: props.auth_user?.name || '',
    guest_phone: props.auth_user?.phone || '',
    address_id: props.addresses?.[0]?.id || null,
    recipient_name: props.auth_user?.name || '',
    phone: props.auth_user?.phone || '',
    governorate_id: null,
    area_id: null,
    street: '',
    building: '',
    floor: '',
    apartment: '',
    landmark: '',
    delivery_date: props.slots?.[0]?.date || '',
    delivery_slot_label: props.slots?.[0]?.slots?.[0]?.label || '',
    delivery_slot_start: props.slots?.[0]?.slots?.[0]?.start || '',
    delivery_slot_end: props.slots?.[0]?.slots?.[0]?.end || '',
    payment_method_id: props.payment_methods?.[0]?.id || null,
    customer_notes: '',
    accept_terms: false,
});

const areas = computed(() => props.governorates.find(g => g.id === form.governorate_id)?.areas || []);
const daySlots = computed(() => props.slots.find(s => s.date === form.delivery_date)?.slots || []);
const selectedArea = computed(() => areas.value.find(a => a.id === form.area_id));
const deliveryFee = computed(() => Number(selectedArea.value?.delivery_fee || 0));
const total = computed(() => Number(props.cart.summary.total) + deliveryFee.value);

watch(() => form.delivery_date, () => {
    const first = daySlots.value[0];
    form.delivery_slot_label = first?.label || '';
    form.delivery_slot_start = first?.start || '';
    form.delivery_slot_end = first?.end || '';
});

const submit = () => form.post('/checkout');
</script>

<template>
    <Head :title="t('checkout.title')" />
    <div class="mx-auto max-w-3xl space-y-6 px-4 py-6">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('checkout.title') }}</h1>
        <div class="flex gap-2 text-xs font-bold">
            <span class="chip" :class="{ 'chip-active': step>=1 }">1 {{ t('checkout.step_info') }}</span>
            <span class="chip" :class="{ 'chip-active': step>=2 }">2 {{ t('checkout.step_address') }}</span>
            <span class="chip" :class="{ 'chip-active': step>=3 }">3 {{ t('checkout.step_slot') }}</span>
            <span class="chip" :class="{ 'chip-active': step>=4 }">4 {{ t('checkout.step_payment') }}</span>
        </div>

        <section v-if="step===1" class="surface-card space-y-4 p-5">
            <template v-if="!auth_user">
                <Link href="/login" class="btn-secondary block text-center">{{ t('auth.login') }}</Link>
                <Link href="/register" class="btn-secondary block text-center">{{ t('auth.register') }}</Link>
                <div v-if="guest_checkout" class="space-y-3 border-t border-black/5 pt-4">
                    <p class="text-sm text-[var(--color-text-muted)]">أو كمّلي كضيفة — الحقول بعلامة * مطلوبة</p>
                    <FormField :label="t('auth.name', 'الاسم')" required :error="form.errors.guest_name">
                        <input v-model="form.guest_name" aria-required="true" autocomplete="name" />
                    </FormField>
                    <FormField :label="t('auth.phone')" required :error="form.errors.guest_phone">
                        <input v-model="form.guest_phone" aria-required="true" inputmode="tel" autocomplete="tel" />
                    </FormField>
                    <button class="btn-primary w-full" @click="step=2">{{ t('checkout.guest_continue') }}</button>
                </div>
            </template>
            <button v-else class="btn-primary w-full" @click="step=2">{{ t('checkout.next', 'التالي') }}</button>
        </section>

        <section v-if="step===2" class="surface-card space-y-4 p-5">
            <div v-if="addresses?.length" class="space-y-2">
                <label v-for="a in addresses" :key="a.id" class="flex items-start gap-2 rounded-xl border border-black/5 p-3">
                    <input type="radio" :value="a.id" v-model="form.address_id" />
                    <span>{{ a.label }} — {{ a.area?.name_ar }} · {{ a.street }}</span>
                </label>
                <button type="button" class="text-sm font-semibold text-[var(--color-denim)]" @click="form.address_id=null">عنوان جديد</button>
            </div>
            <div v-if="!form.address_id" class="grid gap-3">
                <FormField label="اسم المستلم" required :error="form.errors.recipient_name">
                    <input v-model="form.recipient_name" aria-required="true" />
                </FormField>
                <FormField :label="t('auth.phone')" required :error="form.errors.phone">
                    <input v-model="form.phone" aria-required="true" inputmode="tel" />
                </FormField>
                <FormField label="المحافظة" required :error="form.errors.governorate_id">
                    <select v-model="form.governorate_id" aria-required="true">
                        <option :value="null">اختاري المحافظة</option>
                        <option v-for="g in governorates" :key="g.id" :value="g.id">{{ g.name_ar }}</option>
                    </select>
                </FormField>
                <FormField label="المنطقة" required :error="form.errors.area_id">
                    <select v-model="form.area_id" aria-required="true">
                        <option :value="null">اختاري المنطقة</option>
                        <option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name_ar }} — {{ a.delivery_fee }} ج.م</option>
                    </select>
                </FormField>
                <FormField label="الشارع" required :error="form.errors.street">
                    <input v-model="form.street" aria-required="true" />
                </FormField>
                <div class="grid grid-cols-3 gap-2">
                    <FormField label="مبنى" optional>
                        <input v-model="form.building" />
                    </FormField>
                    <FormField label="دور" optional>
                        <input v-model="form.floor" />
                    </FormField>
                    <FormField label="شقة" optional>
                        <input v-model="form.apartment" />
                    </FormField>
                </div>
                <FormField label="علامة مميزة" optional>
                    <input v-model="form.landmark" />
                </FormField>
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary flex-1" @click="step=1">{{ t('checkout.back', 'السابق') }}</button>
                <button class="btn-primary flex-1" @click="step=3">{{ t('checkout.next', 'التالي') }}</button>
            </div>
        </section>

        <section v-if="step===3" class="surface-card space-y-4 p-5">
            <FormField label="يوم التوصيل" required>
                <select v-model="form.delivery_date" aria-required="true">
                    <option v-for="d in slots" :key="d.date" :value="d.date">{{ d.label }}</option>
                </select>
            </FormField>
            <div class="space-y-2">
                <div class="field-label">الموعد <span class="field-required">*</span></div>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="s in daySlots"
                        :key="s.label"
                        type="button"
                        class="chip"
                        :class="{ 'chip-active': form.delivery_slot_label===s.label }"
                        @click="form.delivery_slot_label=s.label; form.delivery_slot_start=s.start; form.delivery_slot_end=s.end"
                    >
                        {{ s.label }}
                    </button>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary flex-1" @click="step=2">{{ t('checkout.back', 'السابق') }}</button>
                <button class="btn-primary flex-1" @click="step=4">{{ t('checkout.next', 'التالي') }}</button>
            </div>
        </section>

        <section v-if="step===4" class="surface-card space-y-4 p-5">
            <div class="space-y-2">
                <div class="field-label">طريقة الدفع <span class="field-required">*</span></div>
                <label v-for="m in payment_methods" :key="m.id" class="flex gap-3 rounded-xl border border-black/5 p-3">
                    <input type="radio" :value="m.id" v-model="form.payment_method_id" />
                    <div>
                        <div class="font-semibold">{{ m.name_ar }}</div>
                        <div class="text-xs text-[var(--color-text-muted)]">{{ m.instructions_ar }}</div>
                    </div>
                </label>
            </div>
            <FormField label="ملاحظات" optional>
                <textarea v-model="form.customer_notes" rows="2" />
            </FormField>
            <label class="flex items-start gap-2 text-sm">
                <input type="checkbox" v-model="form.accept_terms" class="mt-1" aria-required="true" />
                <span>
                    <span class="field-required">*</span>
                    {{ t('checkout.accept_terms', 'أوافق على الشروط والأحكام') }}
                    — <Link href="/terms" class="font-semibold text-[var(--color-denim)]">الشروط</Link>
                </span>
            </label>
            <div class="rounded-xl bg-[var(--color-primary-soft)] p-3 text-sm">
                {{ t('checkout.final_total', 'الإجمالي شامل التوصيل') }}:
                <strong>{{ total }} ج.م</strong>
                <span class="text-[var(--color-text-muted)]">(التوصيل {{ deliveryFee }} ج.م)</span>
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary flex-1" @click="step=3">{{ t('checkout.back', 'السابق') }}</button>
                <button class="btn-primary flex-1" :disabled="form.processing || !form.accept_terms" @click="submit">
                    {{ t('checkout.confirm') }}
                </button>
            </div>
            <div v-if="form.hasErrors" class="field-error">{{ Object.values(form.errors)[0] }}</div>
        </section>
    </div>
</template>
