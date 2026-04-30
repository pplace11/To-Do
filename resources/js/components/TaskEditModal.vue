<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div
            v-if="isOpen"
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="close"
            @keydown.esc="close"
            role="presentation">
            <div
                ref="modalRef"
                class="w-full max-w-md rounded-xl border border-transparent bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-task-title"
                @keydown="trapFocus">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700/60">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-blue-600 dark:text-blue-400" aria-hidden="true">
                            <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                        </svg>
                        <h2 id="edit-task-title" class="text-lg font-bold text-slate-900 dark:text-slate-100">Editar tarefa</h2>
                    </div>
                    <button
                        type="button"
                        @click="close"
                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                        aria-label="Fechar modal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveTask" class="flex flex-col gap-4 px-6 py-5">
                    <div class="flex flex-col gap-1.5">
                        <label for="edit-task-title-input" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            Título <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="edit-task-title-input"
                            ref="firstInput"
                            v-model="form.title"
                            type="text"
                            placeholder="Título da tarefa"
                            aria-required="true"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                            required />
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="edit-task-description" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            Descrição <span class="text-xs font-normal text-slate-500 dark:text-slate-400">(opcional)</span>
                        </label>
                        <textarea
                            id="edit-task-description"
                            v-model="form.description"
                            placeholder="Adicione mais detalhes..."
                            rows="3"
                            class="resize-none rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label for="edit-task-due-date" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Data de vencimento</label>
                            <input
                                id="edit-task-due-date"
                                v-model="form.due_date"
                                type="date"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="edit-task-priority" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Prioridade</label>
                            <select
                                id="edit-task-priority"
                                v-model="form.priority"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                <option value="alta">Alta</option>
                                <option value="media">Média</option>
                                <option value="baixa">Baixa</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-700/60">
                        <button
                            type="button"
                            @click="close"
                            class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps({
    task: Object
});

const emit = defineEmits(['save', 'close']);

const isOpen = ref(false);
const form = ref({});
const modalRef = ref(null);
const firstInput = ref(null);
let previouslyFocused = null;

function open() {
    if (props.task) {
        form.value = { ...props.task };
        previouslyFocused = document.activeElement;
        isOpen.value = true;
        nextTick(() => {
            firstInput.value?.focus();
        });
    }
}

function close() {
    isOpen.value = false;
    nextTick(() => {
        previouslyFocused?.focus();
    });
}

function saveTask() {
    emit('save', form.value);
    close();
}

function trapFocus(event) {
    if (event.key !== 'Tab' || !modalRef.value) return;
    const focusable = modalRef.value.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey) {
        if (document.activeElement === first) {
            event.preventDefault();
            last.focus();
        }
    } else {
        if (document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }
}

defineExpose({ open, close });
</script>
