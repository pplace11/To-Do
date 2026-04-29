<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4" @keydown.esc="close">
        <div
            class="bg-white dark:bg-slate-900 rounded-lg shadow-lg p-6 w-full max-w-md border border-transparent dark:border-slate-700"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-task-title">
            <h2 id="edit-task-title" class="text-xl font-bold mb-4 text-slate-900 dark:text-slate-100">Editar Tarefa</h2>
            <form @submit.prevent="saveTask" class="flex flex-col gap-3">
                <label for="edit-task-title-input" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Título</label>
                <input id="edit-task-title-input" v-model="form.title" type="text" placeholder="Título"
                    class="border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required />

                <label for="edit-task-description" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Descrição</label>
                <textarea id="edit-task-description" v-model="form.description" placeholder="Descrição"
                    class="border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-2 py-1 h-20 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

                <label for="edit-task-due-date" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Data de vencimento</label>
                <input id="edit-task-due-date" v-model="form.due_date" type="date"
                    class="border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" />

                <label for="edit-task-priority" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Prioridade</label>
                <select id="edit-task-priority" v-model="form.priority"
                    class="border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="alta">Alta</option>
                    <option value="media">Média</option>
                    <option value="baixa">Baixa</option>
                </select>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="close"
                        class="px-4 py-2 border border-gray-300 dark:border-slate-600 rounded hover:bg-gray-100 dark:hover:bg-slate-800 text-slate-900 dark:text-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    task: Object
});

const emit = defineEmits(['save', 'close']);

const isOpen = ref(false);
const form = ref({});

function open() {
    if (props.task) {
        form.value = { ...props.task };
        isOpen.value = true;
    }
}

function close() {
    isOpen.value = false;
}

function saveTask() {
    emit('save', form.value);
    close();
}

defineExpose({ open, close });
</script>
