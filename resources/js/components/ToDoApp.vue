<template>
    <div class="flex h-screen bg-white text-gray-800 dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100 overflow-hidden">
        <!-- Skip link -->
        <a href="#todo-main"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:outline-none focus:ring-4 focus:ring-blue-300">
            Pular para o conteúdo principal
        </a>

        <!-- Mobile sidebar backdrop -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-20 bg-black/40 md:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

        <!-- Sidebar -->
        <aside :class="[
            'flex flex-col bg-gray-50 dark:bg-transparent border-r border-gray-200 dark:border-slate-700 pb-3',
            'w-64 shrink-0 fixed inset-y-0 left-0 z-30',
            'transition-transform duration-200 ease-in-out',
            'md:static md:inset-auto md:z-auto md:translate-x-0 md:transition-none',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]">
            <!-- Logo area -->
            <div class="flex items-center h-16 px-4 gap-2 border-b border-gray-200 dark:border-slate-700">
                <img :src="'/image/logo/ToDo.png'" alt="To-Do" class="h-12 w-auto" />
            </div>

            <!-- Nav items -->
            <nav class="flex-1 px-2 py-2 space-y-0.5" aria-label="Navegação principal">
                <button v-for="item in navItems" :key="item.key"
                    @click="activateView(item.key)"
                    :aria-current="sidebarView === item.key ? 'page' : undefined"
                    :class="[
                        'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                        sidebarView === item.key
                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                            : 'text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700'
                    ]">
                    <span class="shrink-0" aria-hidden="true" v-html="item.icon"></span>
                    <span>{{ item.label }}</span>
                    <span v-if="item.count > 0" class="ml-auto text-xs text-gray-400 dark:text-slate-500 tabular-nums">{{ item.count }}</span>
                </button>
            </nav>

            <!-- Add task shortcut -->
            <div class="px-2 pb-2 border-t border-gray-200 dark:border-slate-700 pt-2">
                <a href="/todo/nova"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova tarefa
                </a>
            </div>

            <!-- Bottom action icons -->
            <div class="flex items-center justify-around px-3 py-3 border-t border-gray-200 dark:border-slate-700">
                <button @click="showCalendar = true" :class="['p-2 rounded-lg transition', showCalendar ? 'bg-gray-200 dark:bg-slate-700' : 'hover:bg-gray-200 dark:hover:bg-slate-700']" aria-label="Calendário">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                <button @click="showShareModal = true" :class="['p-2 rounded-lg transition', showShareModal ? 'bg-gray-200 dark:bg-slate-700' : 'hover:bg-gray-200 dark:hover:bg-slate-700']" aria-label="Partilhar tarefa">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5.196-3.796M9 20H4v-2a4 4 0 015.196-3.796M15 7a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <button @click="logout" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition" aria-label="Sair">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="flex flex-wrap items-center justify-between gap-x-2 gap-y-1.5 px-3 sm:px-6 py-2 sm:py-3 border-b border-gray-200 dark:border-slate-700 bg-white dark:bg-transparent shrink-0 min-h-[52px]">
                <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                    <!-- Hamburger (mobile only) -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition shrink-0"
                        :aria-label="sidebarOpen ? 'Fechar menu' : 'Abrir menu'"
                        :aria-expanded="sidebarOpen">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <div ref="optionsMenuRef" class="relative flex items-center gap-1.5">
                            <h1 id="todo-page-title" class="text-xl font-semibold text-gray-800 dark:text-slate-100">{{ activeViewLabel }}</h1>
                            <button @click="showOptionsMenu = !showOptionsMenu" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-slate-700 transition" aria-label="Mais opções" :aria-expanded="showOptionsMenu">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-slate-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <circle cx="4" cy="10" r="1.5"/><circle cx="10" cy="10" r="1.5"/><circle cx="16" cy="10" r="1.5"/>
                                </svg>
                            </button>
                            <div v-if="showOptionsMenu" class="absolute left-0 top-full z-30 mt-2 w-72 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                                <a href="/perfil" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <span>Perfil</span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Nome, email e senha</span>
                                </a>
                                <button @click="toggleTwoFactorFromMenu" class="mt-1 flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <span>{{ currentUser.two_factor_enabled ? 'Desativar dois fatores' : 'Ativar dois fatores' }}</span>
                                    <span :class="['text-xs font-semibold', currentUser.two_factor_enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500']">
                                        {{ currentUser.two_factor_enabled ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5 capitalize hidden sm:block">{{ todayFormatted }}</p>
                    </div>
                    <!-- View toggle tabs -->
                    <div class="flex items-center gap-0.5 pb-0.5 shrink-0">
                        <button
                            @click="viewMode = 'grelha'"
                            :class="['flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium border-b-2 transition', viewMode === 'grelha' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300']">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z" />
                            </svg>
                            <span class="hidden sm:inline">Grelha</span>
                        </button>
                        <button
                            @click="viewMode = 'lista'"
                            :class="['flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium border-b-2 transition', viewMode === 'lista' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300']">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span class="hidden sm:inline">Lista</span>
                        </button>
                    </div>
                </div>

                <!-- Right controls -->
                <div class="flex items-center gap-1 shrink-0">
                    <label for="sort-order" class="sr-only">Ordenação</label>
                    <select v-model="sortOrder" id="sort-order"
                        class="text-sm text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900/30 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/70 focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer">
                        <option value="asc">↑↓ Mais próximas</option>
                        <option value="desc">↑↓ Mais distantes</option>
                    </select>
                    <label for="filter-priority" class="sr-only">Prioridade</label>
                    <select v-model="filterPriority" id="filter-priority"
                        class="text-sm text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900/30 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/70 focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer">
                        <option value="">Todas prioridades</option>
                        <option value="alta">Alta</option>
                        <option value="media">Média</option>
                        <option value="baixa">Baixa</option>
                    </select>
                </div>
            </header>

            <!-- Main scrollable content -->
            <main id="todo-main" tabindex="-1" class="flex-1 overflow-y-auto bg-white dark:bg-transparent">
                <!-- Stats strip -->
                <div class="flex items-center justify-center gap-6 px-6 py-3 border-b border-gray-100 dark:border-slate-800 text-sm">
                    <span class="text-gray-500 dark:text-slate-400"><span class="font-semibold text-gray-700 dark:text-slate-200">{{ totalTasks }}</span> total</span>
                    <span class="text-amber-600"><span class="font-semibold">{{ pendingTasks }}</span> pendentes</span>
                    <span class="text-emerald-600"><span class="font-semibold">{{ completedTasks }}</span> concluídas</span>
                </div>

                <!-- Add task row -->
                <div class="px-6 py-1 border-b border-gray-100 dark:border-slate-800">
                    <a href="/todo/nova"
                        class="flex items-center gap-2 py-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition text-sm font-medium">
                        <span class="h-5 w-5 flex items-center justify-center rounded-full border-2 border-blue-400 text-blue-400 text-sm leading-none font-bold shrink-0">+</span>
                        Adicionar uma tarefa
                    </a>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="flex flex-col items-center justify-center gap-3 py-20 text-gray-400 dark:text-slate-500" role="status" aria-live="polite">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-sm">Carregando tarefas...</span>
                </div>

                <!-- Task list -->
                <TaskList v-else :tasks="filteredTasks" :view-mode="viewMode" @edit="openEditModal" @complete="completeTask" @delete="deleteTask" />
            </main>
        </div>

        <!-- Notificação -->
        <NotificationToast :type="notificationType" :text="notificationText" />

        <!-- Modal de Edição -->
        <TaskEditModal ref="editModal" :task="editingTask" @save="saveTask" />

        <!-- Calendário -->
        <CalendarView :open="showCalendar" :tasks="tasks" @close="showCalendar = false" />

        <!-- Partilhar tarefa -->
        <ShareTaskModal :open="showShareModal" :tasks="tasks" @close="showShareModal = false" @share="shareTask" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import TaskList from './TaskList.vue';
import NotificationToast from './NotificationToast.vue';
import TaskEditModal from './TaskEditModal.vue';
import CalendarView from './CalendarView.vue';
import ShareTaskModal from './ShareTaskModal.vue';

const tasks = ref([]);
const loading = ref(false);
const filterStatus = ref('');
const filterPriority = ref('');
const sortOrder = ref('asc');
const editingTask = ref(null);
const editModal = ref(null);
const showCalendar = ref(false);
const showShareModal = ref(false);
const showOptionsMenu = ref(false);
const sidebarView = ref('todas');
const sidebarOpen = ref(false);
const viewMode = ref('lista');
const isDark = ref(document.documentElement.classList.contains('dark'));
const optionsMenuRef = ref(null);
const currentUser = ref({
    name: '',
    email: '',
    two_factor_enabled: false,
});

function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}

const iconSun = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M5.22 5.22l1.42 1.42m10.72 10.72 1.42 1.42M3 12h2m14 0h2M5.22 18.78l1.42-1.42M17.36 6.64l1.42-1.42M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>`;
const iconStar = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557L3.04 10.395a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>`;
const iconCalendar = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`;
const iconHome = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/></svg>`;
const iconCheck = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;

const notificationText = ref('');
const notificationType = ref('info');
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content') || '';

const navItems = computed(() => [
    { key: 'hoje', label: 'O Meu Dia', icon: iconSun, count: tasks.value.filter(t => t.due_date === todayIso.value && t.status === 'pendente').length },
    { key: 'importante', label: 'Importante', icon: iconStar, count: tasks.value.filter(t => t.priority === 'alta' && t.status === 'pendente').length },
    { key: 'planeado', label: 'Planeado', icon: iconCalendar, count: tasks.value.filter(t => t.due_date).length },
    { key: 'pendentes', label: 'Pendentes', icon: iconCheck, count: tasks.value.filter(t => t.status === 'pendente').length },
    { key: 'todas', label: 'Tarefas', icon: iconHome, count: 0 },
]);

const todayIso = computed(() => new Date().toISOString().split('T')[0]);

const todayFormatted = computed(() => {
    return new Date().toLocaleDateString('pt-PT', { weekday: 'long', day: 'numeric', month: 'long' });
});

const activeViewLabel = computed(() => {
    return navItems.value.find(n => n.key === sidebarView.value)?.label ?? 'Tarefas';
});

function activateView(key) {
    sidebarView.value = key;
    sidebarOpen.value = false; // close drawer on mobile
    if (key === 'pendentes') {
        filterStatus.value = 'pendente';
    } else if (key === 'todas') {
        filterStatus.value = '';
    } else {
        filterStatus.value = '';
    }
}

const filteredTasks = computed(() => {
    let list = [...tasks.value];
    // Sidebar view filters
    if (sidebarView.value === 'hoje') list = list.filter(t => t.due_date === todayIso.value);
    if (sidebarView.value === 'importante') list = list.filter(t => t.priority === 'alta');
    if (sidebarView.value === 'planeado') list = list.filter(t => t.due_date);
    if (sidebarView.value === 'pendentes') list = list.filter(t => t.status === 'pendente');
    // Header filters
    if (filterStatus.value) list = list.filter(t => t.status === filterStatus.value);
    if (filterPriority.value) list = list.filter(t => t.priority === filterPriority.value);
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

function handleDocumentClick(event) {
    if (!showOptionsMenu.value) {
        return;
    }

    if (optionsMenuRef.value && !optionsMenuRef.value.contains(event.target)) {
        showOptionsMenu.value = false;
    }
}

async function fetchCurrentUser() {
    try {
        const res = await fetch('/profile', {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!res.ok) {
            throw new Error('Erro ao carregar perfil');
        }

        currentUser.value = await res.json();
    } catch {
        currentUser.value = {
            name: '',
            email: '',
            two_factor_enabled: false,
        };
    }
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

async function shareTask(payload) {
    const { taskId, email } = payload;

    try {
        const res = await fetch(`/tasks/${taskId}/share`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify({ email }),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            throw new Error(data.message || 'Erro ao partilhar tarefa');
        }

        showShareModal.value = false;
        showNotification(data.message || 'Tarefa partilhada com sucesso!', 'success');
    } catch (error) {
        showNotification(error.message || 'Erro ao partilhar tarefa', 'error');
    }
}

async function toggleTwoFactorFromMenu() {
    try {
        const res = await fetch('/profile/two-factor', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify({ enabled: !currentUser.value.two_factor_enabled }),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            throw new Error(data.message || 'Erro ao atualizar autenticação de dois fatores');
        }

        currentUser.value.two_factor_enabled = !!data.two_factor_enabled;
        showOptionsMenu.value = false;
        showNotification(data.message || 'Autenticação de dois fatores atualizada.', 'success');
    } catch (error) {
        showNotification(error.message || 'Erro ao atualizar autenticação de dois fatores', 'error');
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

onMounted(() => {
    fetchTasks();
    fetchCurrentUser();
    document.addEventListener('click', handleDocumentClick);
});

onUnmounted(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>

<style scoped></style>
