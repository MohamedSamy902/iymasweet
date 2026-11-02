<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
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
    <div class="mx-auto max-w-3xl px-4 py-6 space-y-6">
        <h1 class="font-heading text-3xl text-[var(--color-brown)]">{{ t('checkout.title') }}</h1>
        <div class="flex gap-2 text-xs font-bold">
            <span class="chip" :class="{ 'chip-active': step>=1 }">1 {{ t('checkout.step_info') }}</span>
            <span class="chip" :class="{ 'chip-active': step>=2 }">2 {{ t('checkout.step_address') }}</span>
            <span class="chip" :class="{ 'chip-active': step>=3 }">3 {{ t('checkout.step_slot') }}</span>
            <span class="chip" :class="{ 'chip-active': step>=4 }">4 {{ t('checkout.step_payment') }}</span>
        </div>

        <section v-if="step===1" class="rounded-3xl bg-white p-5 shadow-sm space-y-3">
            <template v-if="!auth_user">
                <Link href="/login" class="btn-secondary block text-center">{{ t('auth.login') }}</Link>
                <Link href="/register" class="btn-secondary block text-center">{{ t('auth.register') }}</Link>
                <div v-if="guest_checkout" class="space-y-2 pt-2">
                    <input v-model="form.guest_name" class="w-full rounded-2xl border px-4 py-2" placeholder="الاسم" />
                    <input v-model="form.guest_phone" class="w-full rounded-2xl border px-4 py-2" placeholder="الموبايل" />
                    <button class="btn-primary w-full" @click="step=2">{{ t('checkout.guest_continue') }}</button>
                </div>
            </template>
            <button v-else class="btn-primary w-full" @click="step=2">التالي</button>
        </section>

        <section v-if="step===2" class="rounded-3xl bg-white p-5 shadow-sm space-y-3">
            <div v-if="addresses?.length" class="space-y-2">
                <label v-for="a in addresses" :key="a.id" class="flex items-start gap-2 rounded-2xl border p-3">
                    <input type="radio" :value="a.id" v-model="form.address_id" />
                    <span>{{ a.label }} — {{ a.area?.name_ar }} · {{ a.street }}</span>
                </label>
                <button type="button" class="text-sm text-[var(--color-denim)]" @click="form.address_id=null">عنوان جديد</button>
            </div>
            <div v-if="!form.address_id" class="grid gap-2">
                <input v-model="form.recipient_name" class="rounded-2xl border px-4 py-2" placeholder="اسم المستلم" />
                <input v-model="form.phone" class="rounded-2xl border px-4 py-2" placeholder="موبايل" />
                <select v-model="form.governorate_id" class="rounded-2xl border px-4 py-2">
                    <option :value="null">المحافظة</option>
                    <option v-for="g in governorates" :key="g.id" :value="g.id">{{ g.name_ar }}</option>
                </select>
                <select v-model="form.area_id" class="rounded-2xl border px-4 py-2">
                    <option :value="null">المنطقة</option>
                    <option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name_ar }} — {{ a.delivery_fee }} ج.م</option>
                </select>
                <input v-model="form.street" class="rounded-2xl border px-4 py-2" placeholder="الشارع" />
                <div class="grid grid-cols-3 gap-2">
                    <input v-model="form.building" class="rounded-2xl border px-3 py-2" placeholder="مبنى" />
                    <input v-model="form.floor" class="rounded-2xl border px-3 py-2" placeholder="دور" />
                    <input v-model="form.apartment" class="rounded-2xl border px-3 py-2" placeholder="شقة" />
                </div>
                <input v-model="form.landmark" class="rounded-2xl border px-4 py-2" placeholder="علامة مميزة" />
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary flex-1" @click="step=1">رجوع</button>
                <button class="btn-primary flex-1" @click="step=3">التالي</button>
            </div>
        </section>

        <section v-if="step===3" class="rounded-3xl bg-white p-5 shadow-sm space-y-3">
            <select v-model="form.delivery_date" class="w-full rounded-2xl border px-4 py-2">
                <option v-for="d in slots" :key="d.date" :value="d.date">{{ d.label }}</option>
            </select>
            <div class="flex flex-wrap gap-2">
                <button v-for="s in daySlots" :key="s.label" type="button" class="chip" :class="{ 'chip-active': form.delivery_slot_label===s.label }" @click="form.delivery_slot_label=s.label; form.delivery_slot_start=s.start; form.delivery_slot_end=s.end">{{ s.label }}</button>
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary flex-1" @click="step=2">رجوع</button>
                <button class="btn-primary flex-1" @click="step=4">التالي</button>
            </div>
        </section>

        <section v-if="step===4" class="rounded-3xl bg-white p-5 shadow-sm space-y-3">
            <label v-for="m in payment_methods" :key="m.id" class="flex gap-3 rounded-2xl border p-3">
                <input type="radio" :value="m.id" v-model="form.payment_method_id" />
                <div>
                    <div class="font-semibold">{{ m.name_ar }}</div>
                    <div class="text-xs text-[var(--color-text-muted)]">{{ m.instructions_ar }}</div>
                </div>
            </label>
            <textarea v-model="form.customer_notes" class="w-full rounded-2xl border px-4 py-2" rows="2" placeholder="ملاحظات" />
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" v-model="form.accept_terms" />
                موافق على <Link href="/terms" class="text-[var(--color-denim)]">الشروط</Link>
            </label>
            <div class="rounded-2xl bg-[var(--color-primary-soft)] p-3 text-sm">
                الإجمالي النهائي: <strong>{{ total }} ج.م</strong> (شامل التوصيل {{ deliveryFee }} ج.م)
            </div>
            <div class="flex gap-2">
                <button class="btn-secondary flex-1" @click="step=3">رجوع</button>
                <button class="btn-primary flex-1" :disabled="form.processing" @click="submit">{{ t('checkout.confirm') }}</button>
            </div>
            <div v-if="form.hasErrors" class="text-sm text-red-500">{{ Object.values(form.errors)[0] }}</div>
        </section>
    </div>
</template>
