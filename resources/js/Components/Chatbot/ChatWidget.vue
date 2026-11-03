<script setup>
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import axios from 'axios';
import { MessageCircle } from '@lucide/vue';
import { useUiText } from '@/Composables/useUiText';

const { t } = useUiText();
const open = ref(false);
const loading = ref(false);
const sessionId = ref(localStorage.getItem('iyma_chat_session') || '');
const messages = ref([]);
const input = ref('');
const messagesEl = ref(null);
const inputEl = ref(null);

const pushBot = (payload) => messages.value.push({ role: 'bot', ...payload });

const scrollToBottom = async () => {
    await nextTick();
    if (messagesEl.value) {
        messagesEl.value.scrollTop = messagesEl.value.scrollHeight;
    }
};

const init = async () => {
    const { data } = await axios.get('/api/chatbot/init', {
        params: { session_id: sessionId.value || undefined },
    });
    sessionId.value = data.session_id;
    localStorage.setItem('iyma_chat_session', data.session_id);
    pushBot({
        type: 'text',
        text: data.welcome,
        buttons: data.quick_replies?.map((q) => ({ label: q.label, message: q.message })),
    });
};

const send = async (text) => {
    if (!text?.trim()) return;
    messages.value.push({ role: 'user', text });
    input.value = '';
    loading.value = true;
    await scrollToBottom();
    try {
        const { data } = await axios.post('/api/chatbot/message', {
            message: text,
            session_id: sessionId.value,
        });
        sessionId.value = data.session_id;
        pushBot(data);
        await scrollToBottom();
    } finally {
        loading.value = false;
    }
};

const onButton = (btn) => {
    if (btn.url) {
        window.location.href = btn.url;
        return;
    }
    if (btn.message) send(btn.message);
};

const close = () => {
    open.value = false;
};

const toggle = async () => {
    open.value = !open.value;
    if (open.value) {
        await scrollToBottom();
        await nextTick();
        inputEl.value?.focus({ preventScroll: true });
    }
};

watch(open, (value) => {
    document.documentElement.classList.toggle('iyma-chat-open', value);
    document.body.style.overflow = value ? 'hidden' : '';
});

const onKeydown = (e) => {
    if (e.key === 'Escape' && open.value) close();
};

onMounted(() => {
    init();
    window.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    document.documentElement.classList.remove('iyma-chat-open');
    document.body.style.overflow = '';
});
</script>

<template>
    <!-- FAB: hidden while sheet is open -->
    <button
        v-show="!open"
        type="button"
        class="iyma-chat-fab fixed z-[70] flex h-14 w-14 items-center justify-center rounded-full bg-[var(--color-primary)] text-2xl text-white shadow-lg"
        :aria-label="t('chat.open', 'فتح المساعد')"
        @click="toggle"
    >
        <MessageCircle class="h-6 w-6" />
    </button>

    <Teleport to="body">
        <div v-if="open" class="iyma-chat-root fixed inset-0 z-[90]">
            <button
                type="button"
                class="absolute inset-0 bg-black/35"
                :aria-label="t('chat.close', 'إغلاق')"
                @click="close"
            />

            <section
                class="iyma-chat-sheet absolute inset-x-0 bottom-0 flex max-h-[min(92dvh,720px)] flex-col overflow-hidden rounded-t-[1.75rem] bg-white shadow-2xl md:inset-x-auto md:end-6 md:bottom-6 md:h-[min(70vh,640px)] md:w-[380px] md:max-h-none md:rounded-3xl"
                role="dialog"
                aria-modal="true"
                :aria-label="t('chat.title', 'مساعد Iyma')"
            >
                <header class="flex shrink-0 items-center justify-between bg-[var(--color-primary)] px-4 py-3 text-white">
                    <div>
                        <div class="font-heading text-lg leading-tight">{{ t('chat.title', 'مساعد Iyma') }}</div>
                        <div class="text-xs text-white/85">{{ t('chat.subtitle', 'مساعد تلقائي للإجابة السريعة') }}</div>
                    </div>
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-lg"
                        :aria-label="t('chat.close', 'إغلاق')"
                        @click="close"
                    >
                        ×
                    </button>
                </header>

                <div ref="messagesEl" class="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain p-3">
                    <div
                        v-for="(m, i) in messages"
                        :key="i"
                        :class="m.role === 'user'
                            ? 'ms-8 rounded-2xl bg-[var(--color-denim-soft)] p-3 text-sm'
                            : 'me-6 rounded-2xl bg-[var(--color-primary-soft)] p-3 text-sm'"
                    >
                        <div>{{ m.text }}</div>
                        <div v-if="m.products?.length" class="mt-2 space-y-2">
                            <a
                                v-for="p in m.products"
                                :key="p.id"
                                :href="p.url"
                                class="flex items-center gap-2 rounded-xl bg-white p-2"
                            >
                                <img
                                    :src="p.image || '/images/logo-placeholder.svg'"
                                    class="h-12 w-12 rounded-lg object-cover"
                                    alt=""
                                />
                                <div>
                                    <div class="font-semibold">{{ p.name }}</div>
                                    <div class="text-xs text-[var(--color-primary-dark)]">
                                        {{ p.price }} ج.م · {{ p.availability_label }}
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div v-if="m.buttons?.length" class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="(b, bi) in m.buttons"
                                :key="bi"
                                type="button"
                                class="rounded-full bg-white px-3 py-1 text-xs font-semibold"
                                @click="onButton(b)"
                            >
                                {{ b.label }}
                            </button>
                        </div>
                    </div>
                    <div v-if="loading" class="donut-loader mx-auto" />
                </div>

                <form
                    class="flex shrink-0 gap-2 border-t bg-white p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]"
                    @submit.prevent="send(input)"
                >
                    <input
                        ref="inputEl"
                        v-model="input"
                        class="min-w-0 flex-1 rounded-full border border-[var(--color-primary-soft)] px-4 py-2.5 text-sm outline-none focus:border-[var(--color-primary)]"
                        :placeholder="t('chat.placeholder', 'اكتبي سؤالك…')"
                        enterkeyhint="send"
                        autocomplete="off"
                    />
                    <button class="btn-primary shrink-0 !px-4 !py-2.5 text-sm" type="submit">
                        {{ t('chat.send', 'إرسال') }}
                    </button>
                </form>
            </section>
        </div>
    </Teleport>
</template>

<style scoped>
.iyma-chat-fab {
    /* above bottom nav; leave room for mini-cart when present */
    inset-inline-end: 1rem;
    bottom: calc(5.75rem + env(safe-area-inset-bottom, 0px));
}

@media (min-width: 768px) {
    .iyma-chat-fab {
        bottom: 1.5rem;
    }
}
</style>
