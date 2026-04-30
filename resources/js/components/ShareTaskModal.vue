<template>
    <Transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Partilhar tarefa">
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60" @click="$emit('close')"></div>

            <div class="relative z-10 w-full max-w-md rounded-xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700 p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-slate-100">Partilhar tarefa</h2>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Escolha a tarefa e indique o e-mail de quem vai receber.</p>
                    </div>
                    <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 dark:text-slate-500 transition" aria-label="Fechar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitShare" class="space-y-4">
                    <div>
                        <label for="share-task" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tarefa</label>
                        <select id="share-task" v-model="selectedTaskId" required
                            class="w-full rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="" disabled>Selecione uma tarefa</option>
                            <option v-for="task in tasks" :key="task.id" :value="String(task.id)">{{ task.title }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="share-email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">E-mail</label>
                        <input id="share-email" v-model.trim="email" type="email" required placeholder="email@exemplo.com"
                            class="w-full rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    </div>

                    <p v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button type="button" @click="$emit('close')"
                            class="px-3 py-2 rounded-md text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="px-3 py-2 rounded-md text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                            {{ submitting ? 'A partilhar...' : 'Partilhar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    open: Boolean,
    tasks: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'share']);

const selectedTaskId = ref('');
const email = ref('');
const error = ref('');
const submitting = ref(false);

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            selectedTaskId.value = '';
            email.value = '';
            error.value = '';
            submitting.value = false;
        }
    }
);

async function submitShare() {
    error.value = '';

    if (!selectedTaskId.value || !email.value) {
        error.value = 'Selecione uma tarefa e informe o e-mail.';
        return;
    }

    submitting.value = true;

    try {
        await emit('share', {
            taskId: selectedTaskId.value,
            email: email.value,
        });
    } catch {
        error.value = 'Não foi possível partilhar a tarefa.';
    } finally {
        submitting.value = false;
    }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
