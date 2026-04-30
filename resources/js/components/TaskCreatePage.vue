<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 text-gray-900 dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100 flex items-start md:items-center justify-center p-4 py-8 md:py-4">
        <a
            href="#create-task-form"
            class="sr-only rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:outline-none focus:ring-4 focus:ring-blue-300">
            Pular para o formulário
        </a>

        <main id="create-task-form" tabindex="-1" class="w-full max-w-xl rounded-xl border border-transparent bg-white p-8 shadow-xl dark:border-slate-700 dark:bg-slate-900" aria-labelledby="create-task-title">
            <div class="mb-6 flex items-center justify-between gap-3 border-b border-slate-100 pb-5 dark:border-slate-700/60">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-blue-600 dark:text-blue-400" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>
                    <h1 id="create-task-title" class="text-xl font-bold text-slate-900 dark:text-slate-100">Nova tarefa</h1>
                </div>
                <a
                    href="/todo"
                    class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                    </svg>
                    Voltar
                </a>
            </div>

            <form @submit.prevent="createTask" class="flex flex-col gap-5" novalidate aria-describedby="form-error">
                <div class="flex flex-col gap-1.5">
                    <label for="create-title" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                        Título <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="create-title"
                        v-model="form.title"
                        type="text"
                        placeholder="Insira o título da tarefa"
                        autocomplete="off"
                        aria-required="true"
                        :aria-invalid="titleError ? 'true' : 'false'"
                        aria-describedby="create-title-error"
                        class="rounded-lg border px-3 py-2 text-slate-900 transition focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-800 dark:text-slate-100"
                        :class="titleError ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-400 dark:border-red-500 dark:bg-red-900/10' : 'border-gray-300 bg-white focus:border-blue-500 dark:border-slate-600'" />
                    <p v-if="titleError" id="create-title-error" class="flex items-center gap-1 text-xs text-red-600 dark:text-red-400" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5 shrink-0" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                        </svg>
                        {{ titleError }}
                    </p>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="create-description" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                        Descrição <span class="text-xs font-normal text-slate-500 dark:text-slate-400">(opcional)</span>
                    </label>
                    <textarea
                        id="create-description"
                        v-model="form.description"
                        placeholder="Adicione mais detalhes à tarefa..."
                        rows="4"
                        class="resize-none rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-1.5">
                        <label for="create-due-date" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Data de vencimento</label>
                        <input
                            id="create-due-date"
                            v-model="form.due_date"
                            type="date"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="create-priority" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Prioridade</label>
                        <select
                            id="create-priority"
                            v-model="form.priority"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                            <option value="alta">Alta</option>
                            <option value="media">Média</option>
                            <option value="baixa">Baixa</option>
                        </select>
                    </div>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-red-500" aria-hidden="true">*</span> Campos obrigatórios
                </p>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5 dark:border-slate-700/60">
                    <a
                        href="/todo"
                        class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        :disabled="saving"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-70">
                        <svg v-if="saving" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ saving ? 'Salvando...' : 'Salvar tarefa' }}
                    </button>
                </div>
            </form>

            <div v-if="errorMessage" id="form-error" class="mt-4 flex items-start gap-2 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-300" role="alert" aria-live="assertive">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                </svg>
                {{ errorMessage }}
            </div>
        </main>

        <NotificationToast :type="notificationType" :text="notificationText" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import NotificationToast from './NotificationToast.vue';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const saving = ref(false);
const errorMessage = ref('');
const titleError = ref('');
const notificationText = ref('');
const notificationType = ref('info');

const form = ref({
    title: '',
    description: '',
    due_date: '',
    priority: 'media',
});

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const date = params.get('date');
    if (date) form.value.due_date = date;
});

async function createTask() {
    titleError.value = '';
    errorMessage.value = '';

    if (!form.value.title.trim()) {
        titleError.value = 'O título é obrigatório.';
        document.getElementById('create-title')?.focus();
        return;
    }

    saving.value = true;

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
            throw new Error('Não foi possível criar a tarefa.');
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
