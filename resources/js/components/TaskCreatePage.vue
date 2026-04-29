<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 text-gray-900 dark:from-slate-900 dark:to-slate-800 dark:text-slate-100 flex items-start justify-center p-4 py-8">
        <main class="w-full max-w-xl rounded-lg border border-transparent bg-white p-6 shadow-lg dark:border-slate-700 dark:bg-slate-900" aria-labelledby="create-task-title">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h1 id="create-task-title" class="text-xl font-bold text-slate-900 dark:text-slate-100">Nova tarefa</h1>
                <div class="flex items-center gap-2">
                    <a
                        href="/todo"
                        class="rounded-md border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                        Voltar
                    </a>
                </div>
            </div>

            <form @submit.prevent="createTask" class="flex flex-col gap-3">
                <label for="create-title" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Titulo</label>
                <input
                    id="create-title"
                    v-model="form.title"
                    type="text"
                    placeholder="Insira o titulo da tarefa"
                    class="rounded border border-gray-300 bg-white px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                    required />

                <label for="create-description" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Descricao</label>
                <textarea
                    id="create-description"
                    v-model="form.description"
                    placeholder="Descricao (opcional)"
                    class="h-24 rounded border border-gray-300 bg-white px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"></textarea>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="create-due-date" class="mb-1 block text-sm font-semibold text-slate-800 dark:text-slate-200">Data de vencimento</label>
                        <input
                            id="create-due-date"
                            v-model="form.due_date"
                            type="date"
                            class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
                    </div>
                    <div>
                        <label for="create-priority" class="mb-1 block text-sm font-semibold text-slate-800 dark:text-slate-200">Prioridade</label>
                        <select
                            id="create-priority"
                            v-model="form.priority"
                            class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                            <option value="alta">Alta</option>
                            <option value="media">Media</option>
                            <option value="baixa">Baixa</option>
                        </select>
                    </div>
                </div>

                <div class="mt-2 flex items-center justify-end gap-2">
                    <a
                        href="/todo"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        :disabled="saving"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-70">
                        {{ saving ? 'Salvando...' : 'Salvar tarefa' }}
                    </button>
                </div>
            </form>

            <p v-if="errorMessage" class="mt-4 rounded bg-red-100 px-3 py-2 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-300" role="alert">
                {{ errorMessage }}
            </p>
        </main>

        <NotificationToast :type="notificationType" :text="notificationText" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import NotificationToast from './NotificationToast.vue';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const saving = ref(false);
const errorMessage = ref('');
const notificationText = ref('');
const notificationType = ref('info');

const form = ref({
    title: '',
    description: '',
    due_date: '',
    priority: 'media',
});

async function createTask() {
    saving.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch('/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify(form.value),
        });

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        if (!response.ok) {
            throw new Error('Nao foi possivel criar a tarefa.');
        }

        notificationType.value = 'success';
        notificationText.value = 'Tarefa criada com sucesso!';
        window.location.href = '/todo';
    } catch (error) {
        notificationType.value = 'error';
        notificationText.value = 'Erro ao criar tarefa.';
        errorMessage.value = error.message;
    } finally {
        saving.value = false;
    }
}

</script>
