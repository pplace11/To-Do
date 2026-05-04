<template>
    <!-- Modal de partilha de tarefa -->
    <!-- Permite ao utilizador escolher uma tarefa da lista e indicar o e-mail do destinatário -->
    <!-- Emite 'close' para fechar e 'share' com { taskId, email } quando o formulário é submetido -->

    <!-- Transição de fade (opacidade) ao abrir/fechar o modal — definida no bloco <style scoped> -->
    <Transition name="fade">

        <!-- Contentor do modal: cobre todo o ecrã (fixed inset-0), centra o cartão, z-index alto -->
        <!-- role="dialog" e aria-modal="true" identificam o elemento como diálogo para leitores de ecrã -->
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Partilhar tarefa">

            <!-- Backdrop semitransparente — clicar fora do cartão emite 'close' para fechar o modal -->
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60" @click="$emit('close')"></div>

            <!-- Cartão do modal: posicionado acima do backdrop (z-10), largura máxima md -->
            <div class="relative z-10 w-full max-w-md rounded-xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700 p-5">

                <!-- Cabeçalho do modal: título, subtítulo e botão de fechar -->
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <!-- Título do modal -->
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-slate-100">Partilhar tarefa</h2>
                        <!-- Instrução resumida para o utilizador -->
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Escolha a tarefa e indique o e-mail de quem vai receber.</p>
                    </div>
                    <!-- Botão X de fechar — emite 'close' ao clicar -->
                    <button @click="$emit('close')" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-400 dark:text-slate-500 transition" aria-label="Fechar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Formulário de partilha — @submit.prevent evita reload e chama submitShare() -->
                <form @submit.prevent="submitShare" class="space-y-4">

                    <!-- Seleção da tarefa a partilhar — ligada a `selectedTaskId` via v-model -->
                    <div>
                        <label for="share-task" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tarefa</label>
                        <!-- A opção desativada serve de placeholder visual para o utilizador -->
                        <!-- O valor de cada opção é convertido para String para consistência com o v-model -->
                        <select id="share-task" v-model="selectedTaskId" required
                            class="w-full rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="" disabled>Selecione uma tarefa</option>
                            <option v-for="task in tasks" :key="task.id" :value="String(task.id)">{{ task.title }}</option>
                        </select>
                    </div>

                    <!-- Campo de e-mail do destinatário — v-model.trim remove espaços automaticamente -->
                    <div>
                        <label for="share-email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">E-mail</label>
                        <input id="share-email" v-model.trim="email" type="email" required placeholder="email@exemplo.com"
                            class="w-full rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    </div>

                    <!-- Mensagem de erro: exibida quando `error` tem conteúdo (validação ou falha do pedido) -->
                    <p v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>

                    <!-- Rodapé do formulário: botões "Cancelar" e "Partilhar" alinhados à direita -->
                    <div class="flex items-center justify-end gap-2 pt-1">
                        <!-- Botão Cancelar: emite 'close' sem submeter o formulário -->
                        <button type="button" @click="$emit('close')"
                            class="px-3 py-2 rounded-md text-sm font-medium border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            Cancelar
                        </button>
                        <!-- Botão Partilhar: desativado durante o envio; texto muda para feedback visual -->
                        <button type="submit" :disabled="submitting"
                            class="px-3 py-2 rounded-md text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                            {{ submitting ? 'A partilhar...' : 'Partilhar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup>
// Importações do Vue:
// - ref: para as variáveis reativas do formulário e estado de envio
// - watch: para limpar o formulário sempre que o modal é aberto
import { ref, watch } from 'vue';

// Props recebidas do componente pai
const props = defineProps({
    open: Boolean,        // Controla a visibilidade do modal (true = aberto)
    tasks: {
        type: Array,
        default: () => [], // Lista de tarefas disponíveis para partilhar
    },
});

// Eventos emitidos pelo componente:
// - 'close': solicita o fecho do modal ao componente pai
// - 'share': envia { taskId, email } ao componente pai para processar a partilha
const emit = defineEmits(['close', 'share']);

// Estado interno do formulário
const selectedTaskId = ref(''); // ID (string) da tarefa selecionada no <select>
const email = ref('');          // E-mail do destinatário (com .trim aplicado pelo v-model)
const error = ref('');          // Mensagem de erro de validação ou falha do pedido
const submitting = ref(false);  // true durante o envio (bloqueia o botão "Partilhar")

// Observa a prop `open` para reiniciar o formulário sempre que o modal é aberto
// Garante que dados de uma partilha anterior não persistam numa nova abertura
watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            selectedTaskId.value = '';  // Limpa a tarefa selecionada
            email.value = '';           // Limpa o e-mail
            error.value = '';           // Limpa mensagens de erro anteriores
            submitting.value = false;   // Garante que o botão não fica bloqueado
        }
    }
);

// Valida os campos e emite o evento 'share' com os dados do formulário
// A validação manual é feita antes da submissão para fornecer feedback imediato
async function submitShare() {
    error.value = ''; // Limpa erros anteriores

    // Validação básica: ambos os campos são obrigatórios
    if (!selectedTaskId.value || !email.value) {
        error.value = 'Selecione uma tarefa e informe o e-mail.';
        return;
    }

    submitting.value = true; // Bloqueia o botão durante o envio

    try {
        // Emite 'share' ao componente pai com o ID da tarefa e o e-mail do destinatário
        // O componente pai é responsável pelo pedido HTTP e pelo fecho do modal em caso de sucesso
        await emit('share', {
            taskId: selectedTaskId.value,
            email: email.value,
        });
    } catch {
        // Captura erros inesperados durante a emissão ou processamento pelo pai
        error.value = 'Não foi possível partilhar a tarefa.';
    } finally {
        submitting.value = false; // Liberta o botão independentemente do resultado
    }
}
</script>

<style scoped>
/* Transição de fade para a entrada e saída do modal */
/* fade-enter-active / fade-leave-active: duração e easing da animação */
/* fade-enter-from / fade-leave-to: estado inicial/final da opacidade (invisible) */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
