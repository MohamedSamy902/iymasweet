<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
const open = ref(false);
const loading = ref(false);
const sessionId = ref(localStorage.getItem('iyma_chat_session') || '');
const messages = ref([]);
const input = ref('');
const quick = ref([]);

const pushBot = (payload) => messages.value.push({ role: 'bot', ...payload });
const init = async () => {
  const { data } = await axios.get('/api/chatbot/init', { params: { session_id: sessionId.value || undefined } });
  sessionId.value = data.session_id;
  localStorage.setItem('iyma_chat_session', data.session_id);
  quick.value = data.quick_replies || [];
  pushBot({ type: 'text', text: data.welcome, buttons: data.quick_replies?.map(q => ({ label: q.label, message: q.message })) });
};
const send = async (text) => {
  if (!text?.trim()) return;
  messages.value.push({ role: 'user', text });
  input.value = '';
  loading.value = true;
  try {
    const { data } = await axios.post('/api/chatbot/message', { message: text, session_id: sessionId.value });
    sessionId.value = data.session_id;
    pushBot(data);
  } finally { loading.value = false; }
};
const onButton = (btn) => {
  if (btn.url) window.location.href = btn.url;
  else if (btn.message) send(btn.message);
};
onMounted(init);
</script>
<template>
  <div class="fixed bottom-24 end-4 z-[60] md:bottom-6">
    <button type="button" class="flex h-14 w-14 items-center justify-center rounded-full bg-[var(--color-primary)] text-2xl text-white shadow-lg" @click="open=!open" aria-label="chat">💬</button>
    <div v-if="open" class="absolute bottom-16 end-0 flex h-[70vh] w-[min(92vw,380px)] flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">
      <div class="bg-[var(--color-primary)] px-4 py-3 text-white">
        <div class="font-heading text-lg">مساعد Iyma</div>
        <div class="text-xs text-white/80">مساعد آلي مش شخص حقيقي</div>
      </div>
      <div class="flex-1 space-y-3 overflow-y-auto p-3">
        <div v-for="(m,i) in messages" :key="i" :class="m.role==='user' ? 'ms-10 rounded-2xl bg-[var(--color-denim-soft)] p-3 text-sm' : 'me-6 rounded-2xl bg-[var(--color-primary-soft)] p-3 text-sm'">
          <div>{{ m.text }}</div>
          <div v-if="m.products?.length" class="mt-2 space-y-2">
            <a v-for="p in m.products" :key="p.id" :href="p.url" class="flex items-center gap-2 rounded-xl bg-white p-2">
              <img :src="p.image || '/images/logo-placeholder.svg'" class="h-12 w-12 rounded-lg object-cover" />
              <div>
                <div class="font-semibold">{{ p.name }}</div>
                <div class="text-xs text-[var(--color-primary-dark)]">{{ p.price }} ج.م · {{ p.availability_label }}</div>
              </div>
            </a>
          </div>
          <div v-if="m.buttons?.length" class="mt-2 flex flex-wrap gap-2">
            <button v-for="(b,bi) in m.buttons" :key="bi" type="button" class="rounded-full bg-white px-3 py-1 text-xs font-semibold" @click="onButton(b)">{{ b.label }}</button>
          </div>
        </div>
        <div v-if="loading" class="donut-loader mx-auto"></div>
      </div>
      <form class="flex gap-2 border-t p-3" @submit.prevent="send(input)">
        <input v-model="input" class="flex-1 rounded-full border px-4 py-2 text-sm" placeholder="اكتبي هنا…" />
        <button class="btn-primary !px-4 !py-2 text-sm" type="submit">إرسال</button>
      </form>
    </div>
  </div>
</template>
