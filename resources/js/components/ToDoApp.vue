<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 text-gray-900 dark:from-slate-900 dark:to-slate-800 dark:text-slate-100 flex flex-col items-center p-4 py-8">
        <a
            href="#todo-main"
            class="sr-only rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:outline-none focus:ring-4 focus:ring-blue-300">
            Pular para o conteudo principal
        </a>

        <div class="w-full max-w-2xl">
            <div class="mb-3 flex justify-center">
                <div class="inline-flex" aria-hidden="true">
                    <img :src="'/image/logo/ToDo.png'" alt="To-Do App" class="h-20 w-auto" />
                </div>
            </div>
            <p class="text-center text-gray-600 dark:text-slate-300 mb-6">Organize suas tarefas com eficiência</p>

            <section aria-label="Resumo das tarefas" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <article class="rounded-lg border border-slate-200 bg-white/85 p-3 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Total</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-slate-100">{{ totalTasks }}</p>
                </article>
                <article class="rounded-lg border border-amber-200 bg-white/85 p-3 text-center shadow-sm dark:border-amber-700/60 dark:bg-slate-900/70">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Pendentes</p>
                    <p class="mt-1 text-2xl font-extrabold text-amber-700 dark:text-amber-300">{{ pendingTasks }}</p>
                </article>
                <article class="rounded-lg border border-emerald-200 bg-white/85 p-3 text-center shadow-sm dark:border-emerald-700/60 dark:bg-slate-900/70">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Concluidas</p>
                    <p class="mt-1 text-2xl font-extrabold text-emerald-700 dark:text-emerald-300">{{ completedTasks }}</p>
                </article>
            </section>

            <main id="todo-main" tabindex="-1" aria-labelledby="todo-page-title"
                class="bg-white dark:bg-slate-900 rounded-lg shadow-lg p-6 border border-transparent dark:border-slate-700">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h1 id="todo-page-title" class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                        Painel de tarefas
                    </h1>
                    <div class="flex items-center gap-2">
                        <a
                            href="/todo/nova"
                            class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300">
                            Nova tarefa
                        </a>
                        <button
                            type="button"
                            @click="logout"
                            class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                            Sair
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <fieldset class="mb-6">
                    <legend class="mb-2 text-sm font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200">Filtros</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="filter-status"
                                class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-200">Estado</label>
                        <select v-model="filterStatus"
                            id="filter-status"
                            class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas</option>
                            <option value="pendente">Pendentes</option>
                            <option value="concluida">Concluídas</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-priority"
                            class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-200">Prioridade</label>
                        <select v-model="filterPriority"
                            id="filter-priority"
                            class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas</option>
                            <option value="alta">Alta</option>
                            <option value="media">Média</option>
                            <option value="baixa">Baixa</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort-order"
                            class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-200">Ordenação</label>
                        <select v-model="sortOrder"
                            id="sort-order"
                            class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="asc">Mais próximas</option>
                            <option value="desc">Mais distantes</option>
                        </select>
                    </div>
                    </div>
                </fieldset>

                <!-- Loading -->
                <div v-if="loading" class="text-center py-4 text-gray-600 dark:text-slate-300" role="status" aria-live="polite">Carregando tarefas...</div>

                <!-- Lista de Tarefas -->
                <TaskList v-else :tasks="filteredTasks" @edit="openEditModal" @complete="completeTask"
                    @delete="deleteTask" />
            </main>
        </div>

        <!-- Notificação -->
        <NotificationToast :type="notificationType" :text="notificationText" />

        <!-- Modal de Edição -->
        <TaskEditModal ref="editModal" :task="editingTask" @save="saveTask" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import TaskList from './TaskList.vue';
import NotificationToast from './NotificationToast.vue';
import TaskEditModal from './TaskEditModal.vue';

const tasks = ref([]);
const loading = ref(false);
const filterStatus = ref('');
const filterPriority = ref('');
const sortOrder = ref('asc');
const editingTask = ref(null);
const editModal = ref(null);

const notificationText = ref('');
const notificationType = ref('info');
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content') || '';

const filteredTasks = computed(() => {
    let list = [...tasks.value];
    if (filterStatus.value) {
        list = list.filter(t => t.status === filterStatus.value);
    }
    if (filterPriority.value) {
        list = list.filter(t => t.priority === filterPriority.value);
    }
    list = list.sort((a, b) => {
        if (!a.due_date) return 1;
        if (!b.due_date) return -1;
        return sortOrder.value === 'asc'
            ? new Date(a.due_date) - new Date(b.due_date)
            : new Date(b.due_date) - new Date(a.due_date);
    });
    return list;
});

const totalTasks = computed(() => tasks.value.length);
const pendingTasks = computed(() => tasks.value.filter(task => task.status === 'pendente').length);
const completedTasks = computed(() => tasks.value.filter(task => task.status === 'concluida').length);

function showNotification(message, type = 'info') {
    notificationText.value = message;
    notificationType.value = type;
}

async function fetchTasks() {
    loading.value = true;
    try {
        const res = await fetch('/tasks');
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        if (!res.ok) throw new Error('Erro ao buscar tarefas');
        tasks.value = await res.json();
    } catch (error) {
        showNotification('Erro ao carregar tarefas', 'error');
    } finally {
        loading.value = false;
    }
}

function openEditModal(task) {
    editingTask.value = task;
    editModal.value?.open();
}

async function saveTask(updatedTask) {
    loading.value = true;
    try {
        const res = await fetch(`/tasks/${updatedTask.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify(updatedTask)
        });
        if (!res.ok) throw new Error('Erro ao atualizar tarefa');
        await fetchTasks();
        showNotification('Tarefa atualizada com sucesso!', 'success');
    } catch (error) {
        showNotification('Erro ao atualizar tarefa', 'error');
    } finally {
        loading.value = false;
    }
}

async function completeTask(task) {
    loading.value = true;
    try {
        const res = await fetch(`/tasks/${task.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify({ status: 'concluida' })
        });
        if (!res.ok) throw new Error('Erro ao concluir tarefa');
        await fetchTasks();
        showNotification('Tarefa concluída!', 'success');
    } catch (error) {
        showNotification('Erro ao concluir tarefa', 'error');
    } finally {
        loading.value = false;
    }
}

async function deleteTask(task) {
    if (!confirm('Tem certeza que deseja excluir esta tarefa?')) return;

    loading.value = true;
    try {
        const res = await fetch(`/tasks/${task.id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            }
        });
        if (!res.ok) throw new Error('Erro ao excluir tarefa');
        await fetchTasks();
        showNotification('Tarefa excluída com sucesso!', 'success');
    } catch (error) {
        showNotification('Erro ao excluir tarefa', 'error');
    } finally {
        loading.value = false;
    }
}

async function logout() {
    try {
        await fetch('/logout', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
        });
    } finally {
        window.location.href = '/';
    }
}

onMounted(fetchTasks);
</script>

<style scoped></style>
