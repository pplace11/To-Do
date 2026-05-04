<template>
    <!-- Componente de notificação toast global -->
    <!-- Exibe uma mensagem temporária no canto inferior direito da ecrã -->
    <!-- Suporta três tipos: 'success', 'error' e 'info', cada um com cor e ícone próprios -->

    <!-- Transição de entrada/saída: desliza suavemente de baixo para cima ao aparecer e desaparece na direção inversa -->
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2">

        <!-- Cartão do toast — só é renderizado enquanto `message` tiver conteúdo -->
        <!-- Posicionado de forma fixa no canto inferior direito (bottom-20 right-4), acima de outros elementos (z-50) -->
        <!-- A cor de fundo é definida dinamicamente por `bgClass` consoante o tipo da notificação -->
        <!-- Acessibilidade: role="alert" para erros (interrompe leitores de ecrã) e role="status" para os restantes -->
        <!-- aria-live="assertive" para erros (anunciados imediatamente) ou "polite" (aguarda pausa do leitor) -->
        <!-- aria-atomic="true" garante que a mensagem é lida como um todo e não parcialmente -->
        <div
            v-if="message"
            class="fixed bottom-20 right-4 z-50 flex max-w-sm items-start gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-xl"
            :class="bgClass"
            :role="props.type === 'error' ? 'alert' : 'status'"
            :aria-live="props.type === 'error' ? 'assertive' : 'polite'"
            aria-atomic="true">

            <!-- Ícone de sucesso: círculo com visto — exibido quando type === 'success' -->
            <svg v-if="props.type === 'success'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm13.36-1.78a.75.75 0 1 0-1.22-.88l-3.26 4.53-1.52-1.52a.75.75 0 0 0-1.06 1.06l2.14 2.14a.75.75 0 0 0 1.14-.09l3.78-5.24Z" clip-rule="evenodd" />
            </svg>

            <!-- Ícone de erro: triângulo de aviso — exibido quando type === 'error' -->
            <svg v-else-if="props.type === 'error'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
            </svg>

            <!-- Ícone de informação: círculo com "i" — exibido para qualquer outro tipo (incluindo 'info') -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
            </svg>

            <!-- Texto da notificação — exibe o conteúdo de `message` (preenchido pelo watcher) -->
            <span>{{ message }}</span>
        </div>
    </Transition>
</template>

<script setup>
// Importações do Vue:
// - ref: para criar a variável reativa `message`
// - computed: para calcular dinamicamente a classe de cor de fundo
// - watch: para reagir a alterações na prop `text`
import { ref, computed, watch } from 'vue';

// Definição das props recebidas pelo componente pai
const props = defineProps({
    type: { type: String, default: 'info' },      // Tipo da notificação: 'success' | 'error' | 'info'
    text: String,                                  // Texto a exibir — alterações nesta prop ativam o watcher
    duration: { type: Number, default: 3000 }      // Duração em milissegundos antes de a mensagem desaparecer (padrão: 3 s)
});

// `message` é a variável reativa que controla a visibilidade do toast no template
// Quando está vazia ('') o v-if oculta o cartão; quando tem conteúdo o toast é exibido
const message = ref('');

// Calcula a classe de cor de fundo consoante o tipo da notificação
// Retorna 'bg-blue-600' como fallback para tipos desconhecidos
const bgClass = computed(() => {
    const map = {
        success: 'bg-emerald-600', // Verde para sucesso
        error: 'bg-red-600',       // Vermelho para erro
        info: 'bg-blue-600',       // Azul para informação
    };
    return map[props.type] ?? 'bg-blue-600';
});

// Observa alterações na prop `text` enviada pelo componente pai
// Quando um novo texto é recebido:
//   1. Preenche `message` para tornar o toast visível
//   2. Agenda a limpeza de `message` após `duration` ms para ocultar o toast automaticamente
watch(() => props.text, (newText) => {
    if (newText) {
        message.value = newText; // Exibe o toast com o novo texto
        setTimeout(() => { message.value = ''; }, props.duration); // Oculta após a duração configurada
    }
});
</script>
