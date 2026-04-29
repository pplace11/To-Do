<template>
    <form @submit.prevent="addTask"
        class="flex flex-col gap-3 mb-6 border-b border-gray-200 dark:border-slate-700 pb-4" aria-labelledby="task-form-title">
        <h2 id="task-form-title" class="text-base font-bold text-slate-900 dark:text-slate-100">Nova tarefa</h2>
        <div>
            <label for="task-title" class="block text-sm font-semibold mb-1 text-slate-800 dark:text-slate-200">Título</label>
            <input id="task-title" v-model="title" type="text" placeholder="Insira o título da tarefa"
                class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
        </div>
        <div>
            <label for="task-description" class="block text-sm font-semibold mb-1 text-slate-800 dark:text-slate-200">Descrição</label>
            <textarea id="task-description" v-model="description" placeholder="Descrição (opcional)"
                class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 h-20 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label for="task-due-date" class="block text-sm font-semibold mb-1 text-slate-800 dark:text-slate-200">Data de
                    Vencimento</label>
                <input id="task-due-date" v-model="due_date" type="date"
                    class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex-1">
                <label for="task-priority" class="block text-sm font-semibold mb-1 text-slate-800 dark:text-slate-200">Prioridade</label>
                <select id="task-priority" v-model="priority"
                    class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="alta">Alta</option>
                    <option value="media">Média</option>
                    <option value="baixa">Baixa</option>
                </select>
            </div>
        </div>
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded font-semibold hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 transition">Adicionar
            Tarefa</button>
    </form>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['task-added']);
const title = ref('');
const description = ref('');
const due_date = ref('');
const priority = ref('media');

function addTask() {
    emit('task-added', {
        title: title.value,
        description: description.value,
        due_date: due_date.value,
        priority: priority.value,
    });
    title.value = '';
    description.value = '';
    due_date.value = '';
    priority.value = 'media';
}
</script>
