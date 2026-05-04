<template>
    <!-- Página de criação de nova tarefa -->
    <!-- Exibe um formulário com título (obrigatório), descrição, data de vencimento e prioridade -->
    <!-- Após criação bem-sucedida, redireciona para /todo e exibe um toast de confirmação -->

    <!-- Contentor de fundo: gradiente azul claro em modo claro; gradiente radial escuro em modo escuro -->
    <!-- Em mobile os itens alinham ao topo (items-start); em desktop centram verticalmente (md:items-center) -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 text-gray-900 dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100 flex items-start md:items-center justify-center p-4 py-8 md:py-4">

        <!-- Skip link de acessibilidade: invisível por defeito (sr-only), visível ao receber foco via teclado -->
        <!-- Permite a utilizadores de teclado saltar diretamente para o formulário -->
        <a
            href="#create-task-form"
            class="sr-only rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:outline-none focus:ring-4 focus:ring-blue-300">
            Pular para o formulário
        </a>

        <!-- Cartão principal do formulário -->
        <!-- id="create-task-form": alvo do skip link e âncora de acessibilidade -->
        <!-- tabindex="-1": permite que o skip link mova o foco para este elemento via JavaScript -->
        <!-- aria-labelledby="create-task-title": liga o cartão ao h1 para leitores de ecrã -->
        <main id="create-task-form" tabindex="-1" class="w-full max-w-xl rounded-xl border border-transparent bg-white p-8 shadow-xl dark:border-slate-700 dark:bg-slate-900" aria-labelledby="create-task-title">

            <!-- Cabeçalho do cartão: ícone + título + botão "Voltar" -->
            <div class="mb-6 flex items-center justify-between gap-3 border-b border-slate-100 pb-5 dark:border-slate-700/60">
                <div class="flex items-center gap-2">
                    <!-- Ícone de "+" decorativo (aria-hidden para não ser lido por leitores de ecrã) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-blue-600 dark:text-blue-400" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>
                    <!-- Título da página — referenciado pelo aria-labelledby do <main> -->
                    <h1 id="create-task-title" class="text-xl font-bold text-slate-900 dark:text-slate-100">Nova tarefa</h1>
                </div>
                <!-- Botão de navegação de regresso à lista de tarefas -->
                <a
                    href="/todo"
                    class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                    <!-- Ícone de seta para a esquerda -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                    </svg>
                    Voltar
                </a>
            </div>

            <!-- Formulário de criação de tarefa -->
            <!-- @submit.prevent evita o reload da página e chama createTask() -->
            <!-- novalidate desativa a validação nativa do browser (a validação é feita no script) -->
            <!-- aria-describedby="form-error" associa o bloco de erro global ao formulário -->
            <form @submit.prevent="createTask" class="flex flex-col gap-5" novalidate aria-describedby="form-error">

                <!-- Campo de título (obrigatório) -->
                <!-- aria-required="true": indica obrigatoriedade a leitores de ecrã -->
                <!-- :aria-invalid: atualizado dinamicamente consoante o estado de erro -->
                <!-- aria-describedby="create-title-error": liga o input à mensagem de erro inline -->
                <!-- :class dinâmico: aplica estilo vermelho quando há erro no título -->
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
                    <!-- Mensagem de erro inline do título — exibida com role="alert" para leitores de ecrã -->
                    <p v-if="titleError" id="create-title-error" class="flex items-center gap-1 text-xs text-red-600 dark:text-red-400" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5 shrink-0" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                        </svg>
                        {{ titleError }}
                    </p>
                </div>

                <!-- Campo de descrição (opcional) — textarea sem redimensionamento manual (resize-none) -->
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

                <!-- Grelha de dois campos: data de vencimento e prioridade -->
                <!-- Em mobile empilha (grid-cols-1); em sm+ coloca lado a lado (sm:grid-cols-2) -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Campo de data de vencimento — input do tipo date, ligado a form.due_date -->
                    <div class="flex flex-col gap-1.5">
                        <label for="create-due-date" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Data de vencimento</label>
                        <input
                            id="create-due-date"
                            v-model="form.due_date"
                            type="date"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
                    </div>
                    <!-- Campo de prioridade — select com três opções: alta, media, baixa -->
                    <!-- Valor padrão 'media' definido no objeto `form` -->
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

                <!-- Legenda de campos obrigatórios -->
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-red-500" aria-hidden="true">*</span> Campos obrigatórios
                </p>

                <!-- Rodapé do formulário: botões "Cancelar" e "Salvar tarefa" alinhados à direita -->
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5 dark:border-slate-700/60">
                    <!-- Botão Cancelar: link de navegação para /todo sem submeter o formulário -->
                    <a
                        href="/todo"
                        class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                        Cancelar
                    </a>
                    <!-- Botão de submissão: desativado durante o envio -->
                    <!-- Ícone de spinner animado (animate-spin) exibido enquanto `saving` é true -->
                    <button
                        type="submit"
                        :disabled="saving"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-70">
                        <!-- Spinner SVG — visível apenas enquanto o pedido está em curso -->
                        <svg v-if="saving" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ saving ? 'Salvando...' : 'Salvar tarefa' }}
                    </button>
                </div>
            </form>

            <!-- Bloco de erro global do formulário -->
            <!-- Exibido quando `errorMessage` tem conteúdo (ex.: falha do servidor) -->
            <!-- role="alert" e aria-live="assertive" garantem anúncio imediato por leitores de ecrã -->
            <div v-if="errorMessage" id="form-error" class="mt-4 flex items-start gap-2 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-300" role="alert" aria-live="assertive">
                <!-- Ícone de triângulo de aviso -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                </svg>
                {{ errorMessage }}
            </div>
        </main>

        <!-- Toast de notificação global — exibe confirmação de sucesso ou erro após a criação da tarefa -->
        <!-- :type e :text são atualizados em createTask() antes do redirecionamento -->
        <NotificationToast :type="notificationType" :text="notificationText" />
    </div>
</template>

<script setup>
// Importações do Vue:
// - ref: para todas as variáveis reativas do componente
// - onMounted: para ler a query string e pré-preencher a data de vencimento
import { ref, onMounted } from 'vue';

// Componente de toast reutilizável para feedback de sucesso/erro
import NotificationToast from './NotificationToast.vue';

// Token CSRF lido do meta tag do HTML — incluído nos cabeçalhos do pedido POST
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Estado de envio — bloqueia o botão "Salvar tarefa" durante o pedido POST
const saving = ref(false);

// Mensagem de erro global exibida abaixo do formulário (ex.: erro 500 do servidor)
const errorMessage = ref('');

// Erro específico do campo título — exibido inline abaixo do input
const titleError = ref('');

// Texto e tipo do toast de notificação — passados como props ao NotificationToast
const notificationText = ref('');       // Mensagem a exibir no toast
const notificationType = ref('info');   // Tipo: 'success' | 'error' | 'info'

// Objeto reativo com os dados do formulário
// `priority` tem valor padrão 'media' para que o <select> comece pré-selecionado
const form = ref({
    title: '',         // Título da tarefa (obrigatório)
    description: '',   // Descrição detalhada (opcional)
    due_date: '',      // Data de vencimento no formato YYYY-MM-DD (opcional)
    priority: 'media', // Prioridade: 'alta' | 'media' | 'baixa'
});

// Após a montagem, verifica se existe o parâmetro `date` na query string da URL
// Permite pré-preencher a data de vencimento quando o utilizador clica num dia no CalendarView
onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const date = params.get('date');
    if (date) form.value.due_date = date; // Pré-preenche a data se fornecida via URL
});

// Valida e submete o formulário de criação de tarefa via POST /tasks
// Fluxo:
//   1. Limpa erros anteriores
//   2. Valida o campo título (obrigatório)
//   3. Envia o pedido POST com os dados do formulário
//   4. Trata respostas 401 (sessão expirada), erros HTTP e sucesso
//   5. Em sucesso: exibe toast e redireciona para /todo
//   6. Em erro: exibe toast e mensagem de erro global
async function createTask() {
    titleError.value = '';   // Limpa erro inline do título
    errorMessage.value = ''; // Limpa erro global

    // Validação do título: não pode ser vazio ou apenas espaços
    if (!form.value.title.trim()) {
        titleError.value = 'O título é obrigatório.';
        document.getElementById('create-title')?.focus(); // Move o foco para o campo com erro
        return;
    }

    saving.value = true; // Bloqueia o botão durante o envio

    try {
        const response = await fetch('/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}), // Inclui CSRF token se disponível
            },
            body: JSON.stringify(form.value), // Envia title, description, due_date e priority
        });

        // Sessão expirada: redireciona para o login
        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        // Outro erro HTTP: lança exceção para o bloco catch
        if (!response.ok) {
            throw new Error('Não foi possível criar a tarefa.');
        }

        // Criação bem-sucedida: exibe toast de sucesso e redireciona para /todo
        notificationType.value = 'success';
        notificationText.value = 'Tarefa criada com sucesso!';
        window.location.href = '/todo';
    } catch (error) {
        // Erro de rede ou HTTP: exibe toast de erro e mensagem global
        notificationType.value = 'error';
        notificationText.value = 'Erro ao criar tarefa.';
        errorMessage.value = error.message;
    } finally {
        saving.value = false; // Liberta o botão independentemente do resultado
    }
}
</script>
