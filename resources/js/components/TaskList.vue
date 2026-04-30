<template>
    <div v-if="tasks.length === 0"
        class="flex flex-col items-center justify-center gap-3 py-20 text-gray-400 dark:text-slate-500"
        role="status" aria-live="polite">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
            class="h-12 w-12 text-gray-200 dark:text-slate-700" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd" />
            <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375Zm9.586 4.594a.75.75 0 0 0-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 0 0-1.06 1.06l1.5 1.5a.75.75 0 0 0 1.116-.062l3-3.75Z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-medium">Nenhuma tarefa encontrada</p>
        <p class="text-xs text-gray-300 dark:text-slate-600">Adicione uma nova tarefa para começar!</p>
    </div>

    <ul v-else-if="viewMode === 'lista'" aria-label="Lista de tarefas">
        <li v-for="task in tasks" :key="task.id"
            class="group flex items-center gap-3 px-4 sm:px-6 py-2.5 sm:py-3 border-b border-gray-100 dark:border-slate-700/50 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">

            <!-- Circle checkbox -->
            <button
                @click="task.status === 'pendente' ? $emit('complete', task) : null"
                :aria-label="task.status === 'pendente' ? `Concluir tarefa ${task.title}` : `Tarefa ${task.title} já concluída`"
                :disabled="task.status === 'concluida'"
                :class="[
                    'shrink-0 h-5 w-5 rounded-full border-2 flex items-center justify-center transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400',
                    task.status === 'concluida'
                        ? 'border-emerald-400 bg-emerald-400 cursor-default'
                        : priorityBorderColor(task.priority) + ' hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer'
                ]">
                <svg v-if="task.status === 'concluida'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </button>

            <!-- Task content -->
            <div class="flex-1 min-w-0">
                <p :class="['text-sm font-medium truncate', task.status === 'concluida' ? 'line-through text-gray-400 dark:text-slate-500' : 'text-gray-800 dark:text-slate-200']">
                    {{ task.title }}
                </p>
                <p v-if="task.description" class="text-xs text-gray-400 dark:text-slate-500 truncate mt-0.5">{{ task.description }}</p>
            </div>

            <!-- Meta info -->
            <div class="flex items-center gap-2 shrink-0 text-xs text-gray-400 dark:text-slate-500">
                <span v-if="task.due_date" class="hidden sm:inline">{{ task.due_date }}</span>
                <span :class="['px-1.5 py-0.5 rounded font-medium', priorityBadge(task.priority)]">
                    {{ priorityShortLabel(task.priority) }}
                </span>
            </div>

            <!-- Actions (always visible on mobile, hover on desktop) -->
            <div class="flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity shrink-0">
                <button @click="$emit('edit', task)"
                    :aria-label="`Editar tarefa ${task.title}`"
                    class="p-1.5 rounded-md text-gray-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                </button>
                <button @click="$emit('delete', task)"
                    :aria-label="`Excluir tarefa ${task.title}`"
                    class="p-1.5 rounded-md text-gray-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </li>
    </ul>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 p-4" aria-label="Tarefas em grelha">
        <article v-for="task in tasks" :key="task.id"
            class="group rounded-xl border border-gray-200 dark:border-slate-700 bg-transparent p-4 flex flex-col gap-3 hover:shadow-md transition-shadow">
            <div class="flex items-start gap-3">
                <button
                    @click="task.status === 'pendente' ? $emit('complete', task) : null"
                    :aria-label="task.status === 'pendente' ? `Concluir tarefa ${task.title}` : `Tarefa ${task.title} já concluída`"
                    :disabled="task.status === 'concluida'"
                    :class="[
                        'mt-0.5 shrink-0 h-5 w-5 rounded-full border-2 flex items-center justify-center transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400',
                        task.status === 'concluida'
                            ? 'border-emerald-400 bg-emerald-400 cursor-default'
                            : priorityBorderColor(task.priority) + ' hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer'
                    ]">
                    <svg v-if="task.status === 'concluida'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
                <div class="min-w-0">
                    <p :class="['text-sm font-semibold', task.status === 'concluida' ? 'line-through text-gray-400 dark:text-slate-500' : 'text-gray-800 dark:text-slate-100']">
                        {{ task.title }}
                    </p>
                    <p v-if="task.description" class="text-xs text-gray-500 dark:text-slate-400 mt-1 line-clamp-3">
                        {{ task.description }}
                    </p>
                </div>
            </div>

            <div class="mt-auto flex items-center justify-between gap-2 text-xs text-gray-500 dark:text-slate-400">
                <span>{{ task.due_date || 'Sem data' }}</span>
                <span :class="['px-2 py-0.5 rounded font-medium', priorityBadge(task.priority)]">
                    {{ priorityShortLabel(task.priority) }}
                </span>
            </div>

            <div class="flex items-center justify-end gap-1 pt-1 border-t border-gray-100 dark:border-slate-700">
                <button @click="$emit('edit', task)"
                    :aria-label="`Editar tarefa ${task.title}`"
                    class="p-1.5 rounded-md text-gray-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                </button>
                <button @click="$emit('delete', task)"
                    :aria-label="`Excluir tarefa ${task.title}`"
                    class="p-1.5 rounded-md text-gray-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </article>
    </div>
</template>

<script setup>
defineProps({
    tasks: Array,
    viewMode: {
        type: String,
        default: 'lista',
    },
});
defineEmits(['edit', 'complete', 'delete']);

function priorityBorderColor(priority) {
    if (priority === 'alta') return 'border-red-400';
    if (priority === 'media') return 'border-amber-400';
    return 'border-blue-300';
}

function priorityBadge(priority) {
    if (priority === 'alta') return 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400';
    if (priority === 'media') return 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
    return 'bg-blue-50 text-blue-500 dark:bg-blue-900/30 dark:text-blue-400';
}

function priorityShortLabel(priority) {
    if (priority === 'alta') return 'Alta';
    if (priority === 'media') return 'Média';
    return 'Baixa';
}
</script>
