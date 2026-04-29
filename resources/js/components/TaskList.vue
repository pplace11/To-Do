<template>
    <div v-if="tasks.length === 0" class="text-center text-gray-600 dark:text-slate-300 py-8" role="status" aria-live="polite">
        <p>Nenhuma tarefa encontrada. Adicione uma nova!</p>
    </div>
    <ul v-else class="space-y-3" aria-label="Lista de tarefas">
        <li v-for="task in tasks" :key="task.id"
            class="bg-gray-100 dark:bg-slate-800 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-gray-200 dark:hover:bg-slate-700 transition border border-transparent dark:border-slate-700">
            <div class="flex-1">
                <h3 class="flex items-center gap-2 font-semibold text-lg" :class="priorityColor(task.priority)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path d="M9 2.25a.75.75 0 0 0-.75.75v1.5H6A2.25 2.25 0 0 0 3.75 6.75v12A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25v-12A2.25 2.25 0 0 0 18 4.5h-2.25V3A.75.75 0 0 0 15 2.25H9Zm5.25 2.25h-4.5v-.75h4.5v.75ZM8.25 9a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5H9A.75.75 0 0 1 8.25 9Zm0 3.75A.75.75 0 0 1 9 12h6a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Zm.75 3a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5H9Z" />
                    </svg>
                    <span>{{ task.title }}</span>
                </h3>
                <p v-if="task.description" class="text-sm text-gray-600 dark:text-slate-300 mt-1">{{ task.description }}
                </p>
                <div class="text-xs text-gray-500 dark:text-slate-400 mt-2 flex flex-wrap gap-3">
                    <span>
                        <span class="font-semibold">Vencimento:</span>
                        {{ task.due_date || 'Sem data' }}
                    </span>
                    <span>
                        <span class="font-semibold">{{ priorityLabel(task.priority) }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 font-semibold" :class="statusColor(task.status)">
                        <svg v-if="task.status === 'pendente'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 1.5a10.5 10.5 0 1 0 10.5 10.5A10.512 10.512 0 0 0 12 1.5ZM12.75 7.5a.75.75 0 0 0-1.5 0v5.25c0 .199.079.39.22.53l2.25 2.25a.75.75 0 1 0 1.06-1.06l-2.03-2.03V7.5Z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm13.36-1.78a.75.75 0 1 0-1.22-.88l-3.26 4.53-1.52-1.52a.75.75 0 0 0-1.06 1.06l2.14 2.14a.75.75 0 0 0 1.14-.09l3.78-5.24Z" clip-rule="evenodd" />
                        </svg>
                        {{ task.status === 'pendente' ? 'Pendente' : 'Concluida' }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2 justify-end sm:flex-col">
                <button @click="$emit('edit', task)"
                    :aria-label="`Editar tarefa ${task.title}`"
                    class="inline-flex items-center justify-center gap-1 text-blue-700 dark:text-blue-300 text-sm font-semibold px-2 py-1 bg-blue-50 dark:bg-blue-950/50 rounded hover:bg-blue-100 dark:hover:bg-blue-900/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path d="M16.862 3.487a2.625 2.625 0 1 1 3.712 3.712l-11.25 11.25a2.25 2.25 0 0 1-.95.55l-3.7 1.01a.75.75 0 0 1-.92-.92l1.01-3.7a2.25 2.25 0 0 1 .55-.95l11.25-11.25Z" />
                    </svg>
                    Editar
                </button>
                <button v-if="task.status === 'pendente'" @click="$emit('complete', task)"
                    :aria-label="`Concluir tarefa ${task.title}`"
                    class="inline-flex items-center justify-center gap-1 text-emerald-700 dark:text-emerald-300 text-sm font-semibold px-2 py-1 bg-emerald-50 dark:bg-emerald-950/50 rounded hover:bg-emerald-100 dark:hover:bg-emerald-900/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm13.36-1.78a.75.75 0 1 0-1.22-.88l-3.26 4.53-1.52-1.52a.75.75 0 0 0-1.06 1.06l2.14 2.14a.75.75 0 0 0 1.14-.09l3.78-5.24Z" clip-rule="evenodd" />
                    </svg>
                    Concluir
                </button>
                <button @click="$emit('delete', task)"
                    :aria-label="`Excluir tarefa ${task.title}`"
                    class="inline-flex items-center justify-center gap-1 text-red-700 dark:text-red-300 text-sm font-semibold px-2 py-1 bg-red-50 dark:bg-red-950/50 rounded hover:bg-red-100 dark:hover:bg-red-900/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07A3 3 0 0 1 15.916 22.5H8.084a3 3 0 0 1-2.992-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478 48.567 48.567 0 0 1 3.878-.512v-.227A2.25 2.25 0 0 1 9.75 2.25h4.5a2.25 2.25 0 0 1 2.25 2.228ZM10.5 6a.75.75 0 0 0-1.5 0v11.25a.75.75 0 0 0 1.5 0V6Zm4.5 0a.75.75 0 0 0-1.5 0v11.25a.75.75 0 0 0 1.5 0V6Z" clip-rule="evenodd" />
                    </svg>
                    Excluir
                </button>
            </div>
        </li>
    </ul>
</template>

<script setup>
const props = defineProps({
    tasks: Array
});

function priorityColor(priority) {
    if (priority === 'alta') return 'text-red-700 dark:text-red-300';
    if (priority === 'media') return 'text-amber-700 dark:text-amber-300';
    return 'text-emerald-700 dark:text-emerald-300';
}

function priorityLabel(priority) {
    if (priority === 'alta') return 'Prioridade: Alta';
    if (priority === 'media') return 'Prioridade: Media';
    return 'Prioridade: Baixa';
}

function statusColor(status) {
    return status === 'concluida'
        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200'
        : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200';
}
</script>
