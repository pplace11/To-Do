<template>
    <!-- Formulário inline de criação rápida de tarefa -->
    <!-- Utilizado dentro do painel principal (ToDoApp) para adicionar tarefas sem sair da página -->
    <!-- Após a submissão, emite 'task-added' com os dados e limpa todos os campos automaticamente -->

    <!-- @submit.prevent evita o reload da página e chama addTask() -->
    <!-- aria-labelledby="task-form-title": associa o formulário ao h2 para leitores de ecrã -->
    <form @submit.prevent="addTask"
        class="flex flex-col gap-3 mb-6 border-b border-gray-200 dark:border-slate-700 pb-4" aria-labelledby="task-form-title">

        <!-- Título do formulário — referenciado pelo aria-labelledby -->
        <h2 id="task-form-title" class="text-base font-bold text-slate-900 dark:text-slate-100">Nova tarefa</h2>

        <!-- Campo de título (obrigatório) — ligado a `title` via v-model -->
        <div>
            <label for="task-title" class="block text-sm font-semibold mb-1 text-slate-800 dark:text-slate-200">Título</label>
            <input id="task-title" v-model="title" type="text" placeholder="Insira o título da tarefa"
                class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
        </div>

        <!-- Campo de descrição (opcional) — textarea ligado a `description` via v-model -->
        <div>
            <label for="task-description" class="block text-sm font-semibold mb-1 text-slate-800 dark:text-slate-200">Descrição</label>
            <textarea id="task-description" v-model="description" placeholder="Descrição (opcional)"
                class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 h-20 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <!-- Linha de campos secundários: data de vencimento e prioridade -->
        <!-- Em mobile empilha (flex-col); em sm+ coloca lado a lado (sm:flex-row) -->
        <div class="flex flex-col sm:flex-row gap-3">

            <!-- Campo de data de vencimento — input do tipo date, ligado a `due_date` -->
            <div class="flex-1">
                <label for="task-due-date" class="block text-sm font-semibold mb-1 text-slate-800 dark:text-slate-200">Data de
                    Vencimento</label>
                <input id="task-due-date" v-model="due_date" type="date"
                    class="w-full border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <!-- Campo de prioridade — select com três opções: alta, media, baixa -->
            <!-- Valor padrão 'media' definido na variável `priority` -->
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

        <!-- Botão de submissão: aciona addTask() via @submit.prevent no formulário -->
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded font-semibold hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 transition">Adicionar
            Tarefa</button>
    </form>
</template>

<script setup>
// Importação do Vue:
// - ref: para criar as variáveis reativas dos campos do formulário
import { ref } from 'vue';

// Evento emitido pelo componente:
// - 'task-added': enviado ao componente pai com os dados da nova tarefa após submissão
const emit = defineEmits(['task-added']);

// Campos do formulário — cada um ligado ao respetivo input/select/textarea via v-model
const title = ref('');          // Título da tarefa (obrigatório)
const description = ref('');   // Descrição adicional (opcional)
const due_date = ref('');       // Data de vencimento no formato YYYY-MM-DD (opcional)
const priority = ref('media'); // Prioridade: 'alta' | 'media' | 'baixa' (padrão: 'media')

// Emite o evento 'task-added' com os valores atuais dos campos
// Após a emissão, limpa todos os campos para preparar o formulário para uma nova entrada
function addTask() {
    emit('task-added', {
        title: title.value,
        description: description.value,
        due_date: due_date.value,
        priority: priority.value,
    });

    // Limpeza dos campos após emissão
    title.value = '';
    description.value = '';
    due_date.value = '';
    priority.value = 'media'; // Repõe o valor padrão da prioridade
}
</script>
