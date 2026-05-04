<template>
    <!-- Modal de edição de tarefa -->
    <!-- Abre-se programaticamente via open() exposto ao componente pai -->
    <!-- Emite 'save' com os dados atualizados e 'close' quando é fechado -->
    <!-- Implementa armadilha de foco (trapFocus) e restauração do foco anterior ao fechar -->

    <!-- Transição de fade (opacidade) ao abrir/fechar o modal -->
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0">

        <!-- Overlay: cobre todo o ecrã, fundo semitransparente com backdrop-blur -->
        <!-- @click.self: fecha o modal apenas quando o clique é no overlay e não no cartão interior -->
        <!-- @keydown.esc: permite fechar o modal com a tecla Escape -->
        <!-- role="presentation": o overlay não tem semântica própria para leitores de ecrã -->
        <div
            v-if="isOpen"
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="close"
            @keydown.esc="close"
            role="presentation">

            <!-- Cartão do modal -->
            <!-- ref="modalRef": referência usada por trapFocus para encontrar elementos focáveis -->
            <!-- role="dialog" + aria-modal="true": identificam o elemento como diálogo para leitores de ecrã -->
            <!-- aria-labelledby="edit-task-title": associa o diálogo ao h2 para anúncio do nome -->
            <!-- @keydown="trapFocus": interceta Tab/Shift+Tab para manter o foco dentro do modal -->
            <div
                ref="modalRef"
                class="w-full max-w-md rounded-xl border border-transparent bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-task-title"
                @keydown="trapFocus">

                <!-- Cabeçalho do modal: ícone de edição + título + botão de fechar -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700/60">
                    <div class="flex items-center gap-2">
                        <!-- Ícone de lápis decorativo (aria-hidden para não ser lido) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-blue-600 dark:text-blue-400" aria-hidden="true">
                            <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                        </svg>
                        <!-- Título do modal — referenciado pelo aria-labelledby do cartão -->
                        <h2 id="edit-task-title" class="text-lg font-bold text-slate-900 dark:text-slate-100">Editar tarefa</h2>
                    </div>
                    <!-- Botão X de fechar — chama close() ao clicar -->
                    <button
                        type="button"
                        @click="close"
                        class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                        aria-label="Fechar modal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Formulário de edição — @submit.prevent evita reload e chama saveTask() -->
                <form @submit.prevent="saveTask" class="flex flex-col gap-4 px-6 py-5">

                    <!-- Campo de título (obrigatório) -->
                    <!-- ref="firstInput": referência para mover o foco automaticamente ao abrir o modal -->
                    <!-- aria-required="true": indica obrigatoriedade a leitores de ecrã -->
                    <div class="flex flex-col gap-1.5">
                        <label for="edit-task-title-input" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            Título <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="edit-task-title-input"
                            ref="firstInput"
                            v-model="form.title"
                            type="text"
                            placeholder="Título da tarefa"
                            aria-required="true"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                            required />
                    </div>

                    <!-- Campo de descrição (opcional) — textarea sem redimensionamento manual -->
                    <div class="flex flex-col gap-1.5">
                        <label for="edit-task-description" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            Descrição <span class="text-xs font-normal text-slate-500 dark:text-slate-400">(opcional)</span>
                        </label>
                        <textarea
                            id="edit-task-description"
                            v-model="form.description"
                            placeholder="Adicione mais detalhes..."
                            rows="3"
                            class="resize-none rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"></textarea>
                    </div>

                    <!-- Grelha de dois campos lado a lado: data de vencimento e prioridade -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Campo de data de vencimento — input do tipo date -->
                        <div class="flex flex-col gap-1.5">
                            <label for="edit-task-due-date" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Data de vencimento</label>
                            <input
                                id="edit-task-due-date"
                                v-model="form.due_date"
                                type="date"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
                        </div>

                        <!-- Campo de prioridade — select com três opções: alta, media, baixa -->
                        <div class="flex flex-col gap-1.5">
                            <label for="edit-task-priority" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Prioridade</label>
                            <select
                                id="edit-task-priority"
                                v-model="form.priority"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-slate-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                <option value="alta">Alta</option>
                                <option value="media">Média</option>
                                <option value="baixa">Baixa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rodapé do formulário: botões "Cancelar" e "Salvar alterações" alinhados à direita -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-700/60">
                        <!-- Botão Cancelar: chama close() sem submeter o formulário -->
                        <button
                            type="button"
                            @click="close"
                            class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">
                            Cancelar
                        </button>
                        <!-- Botão de submissão: emite 'save' com os dados atualizados e fecha o modal -->
                        <button
                            type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup>
// Importações do Vue:
// - ref: para variáveis reativas e referências de elementos do DOM
// - nextTick: para aguardar a atualização do DOM antes de mover o foco
import { ref, nextTick } from 'vue';

// Props recebidas do componente pai
const props = defineProps({
    task: Object // Objeto da tarefa a editar — copiado para `form` ao abrir o modal
});

// Eventos emitidos pelo componente:
// - 'save': enviado com os dados atualizados do formulário quando o utilizador guarda
// - 'close': enviado quando o modal é fechado (sem guardar)
const emit = defineEmits(['save', 'close']);

// Controla a visibilidade do modal (v-if no template)
const isOpen = ref(false);

// Cópia local dos dados da tarefa — editada pelo utilizador sem alterar o objeto original
const form = ref({});

// Referência ao elemento do cartão do modal — usada por trapFocus para encontrar elementos focáveis
const modalRef = ref(null);

// Referência ao primeiro input focável do modal — recebe o foco automaticamente ao abrir
const firstInput = ref(null);

// Guarda o elemento que estava em foco antes de abrir o modal
// Permite restaurar o foco ao fechar (boa prática de acessibilidade)
let previouslyFocused = null;

// Abre o modal com os dados da tarefa recebida via prop
// 1. Copia a tarefa para `form` (spread para evitar mutação do objeto original)
// 2. Guarda o elemento com foco atual para restauração posterior
// 3. Torna o modal visível
// 4. Após o próximo tick do DOM, move o foco para o primeiro input
function open() {
    if (props.task) {
        form.value = { ...props.task };               // Cópia superficial da tarefa
        previouslyFocused = document.activeElement;   // Guarda o foco atual
        isOpen.value = true;                          // Torna o modal visível
        nextTick(() => {
            firstInput.value?.focus();                // Move o foco para o campo de título
        });
    }
}

// Fecha o modal e restaura o foco para o elemento que estava ativo antes da abertura
// nextTick garante que o DOM é atualizado antes de tentar mover o foco
function close() {
    isOpen.value = false;
    nextTick(() => {
        previouslyFocused?.focus(); // Devolve o foco ao elemento anterior
    });
}

// Emite o evento 'save' com os dados atualizados do formulário e fecha o modal
// O componente pai é responsável por processar o pedido HTTP de atualização
function saveTask() {
    emit('save', form.value);
    close();
}

// Armadilha de foco (focus trap): impede que o foco saia do modal enquanto está aberto
// Interceta a tecla Tab (avançar) e Shift+Tab (recuar):
//   - Se o foco está no primeiro elemento e o utilizador pressiona Shift+Tab → move para o último
//   - Se o foco está no último elemento e o utilizador pressiona Tab → move para o primeiro
// Seleciona todos os elementos interativos nativamente focáveis dentro do modalRef
function trapFocus(event) {
    if (event.key !== 'Tab' || !modalRef.value) return;
    const focusable = modalRef.value.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey) {
        // Shift+Tab no primeiro elemento → salta para o último
        if (document.activeElement === first) {
            event.preventDefault();
            last.focus();
        }
    } else {
        // Tab no último elemento → salta para o primeiro
        if (document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }
}

// Expõe open() e close() ao componente pai para controlo programático do modal
// O pai pode chamar modalRef.open(task) para abrir e modalRef.close() para fechar
defineExpose({ open, close });
</script>
