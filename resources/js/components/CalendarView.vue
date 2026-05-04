<template>
    <!-- Transicao de fade ao abrir/fechar o modal do calendario -->
    <Transition name="fade">
        <!-- Modal principal: visivel apenas quando 'open' for true -->
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Calendário de tarefas">
            <!-- Backdrop: fundo semi-transparente; ao clicar emite o evento 'close' -->
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60" @click="$emit('close')"></div>

            <!-- Painel principal: divide-se em lado do calendario e lado da lista de tarefas -->
            <div class="relative z-10 flex flex-col md:flex-row w-full max-w-4xl max-h-[90vh] rounded-xl shadow-2xl bg-white dark:bg-slate-800 overflow-hidden">

                <!-- Lado esquerdo: calendario mensal -->
                <div class="flex flex-col flex-1 min-w-0 p-5">
                    <!-- Cabecalho do calendario: navegacao entre meses e label do mes atual -->
                    <div class="flex items-center justify-between mb-4">
                        <!-- Botao para ir ao mes anterior -->
                        <button @click="prevMonth"
                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition text-gray-500 dark:text-slate-300"
                            aria-label="Mês anterior">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <!-- Label com o nome do mes e ano atuais -->
                        <h2 class="text-base font-semibold text-gray-800 dark:text-slate-100 capitalize">
                            {{ monthLabel }}
                        </h2>
                        <!-- Botao para ir ao proximo mes -->
                        <button @click="nextMonth"
                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition text-gray-500 dark:text-slate-300"
                            aria-label="Próximo mês">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Cabecalhos dos dias da semana (Dom, Seg, ..., Sab) -->
                    <div class="grid grid-cols-7 mb-1">
                        <div v-for="d in weekDays" :key="d"
                            class="text-center text-xs font-medium text-gray-400 dark:text-slate-500 py-1">
                            {{ d }}
                        </div>
                    </div>

                    <!-- Grelha de dias do mes -->
                    <div class="grid grid-cols-7 gap-1 flex-1">
                        <!-- Celulas vazias para alinhar o primeiro dia da semana corretamente -->
                        <div v-for="n in firstDayOfWeek" :key="'e' + n"></div>

                        <!-- Botao de cada dia do mes -->
                        <button
                            v-for="day in daysInMonth"
                            :key="day"
                            @click="selectDay(day)"
                            :aria-label="`${day} ${monthLabel}`"
                            :class="[
                                'relative flex flex-col items-center rounded-lg py-1.5 px-1 text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400',
                                // Destaque azul forte para o dia de hoje
                                isToday(day)
                                    ? 'bg-blue-600 text-white font-bold'
                                    // Destaque suave para o dia selecionado
                                    : selectedDay === day
                                        ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-semibold'
                                        // Estilo neutro com hover para os restantes dias
                                        : 'text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700',
                            ]">
                            <span>{{ day }}</span>
                            <!-- Pontos de prioridade: mostrados se existirem tarefas neste dia (max 3 pontos) -->
                            <div v-if="tasksOnDay(day).length > 0" class="flex gap-0.5 mt-0.5">
                                <span
                                    v-for="(t, i) in tasksOnDay(day).slice(0, 3)"
                                    :key="i"
                                    :class="[
                                        'h-1.5 w-1.5 rounded-full',
                                        // No dia de hoje os pontos sao brancos; nos restantes usam a cor da prioridade
                                        isToday(day) ? 'bg-white/70' : priorityDot(t.priority)
                                    ]">
                                </span>
                            </div>
                        </button>
                    </div>

                    <!-- Legenda das cores de prioridade -->
                    <div class="flex items-center gap-4 mt-4 text-xs text-gray-400 dark:text-slate-500">
                        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-red-400 inline-block"></span> Alta</span>
                        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-400 inline-block"></span> Média</span>
                        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-blue-400 inline-block"></span> Baixa</span>
                    </div>
                </div>

                <!-- Lado direito: painel com as tarefas do dia selecionado -->
                <div class="w-full md:w-72 flex flex-col border-t md:border-t-0 md:border-l border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-900 overflow-y-auto">
                    <!-- Cabecalho do painel: mostra o dia selecionado e o numero de tarefas -->
                    <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-gray-100 dark:border-slate-700">
                        <div>
                            <!-- Label do dia selecionado ou mensagem de instrucao -->
                            <p class="text-sm font-semibold text-gray-800 dark:text-slate-100">
                                {{ selectedDay ? selectedDayLabel : 'Selecione um dia' }}
                            </p>
                            <!-- Contagem de tarefas para o dia selecionado -->
                            <p v-if="selectedDay" class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">
                                {{ tasksOnDay(selectedDay).length }} tarefa{{ tasksOnDay(selectedDay).length !== 1 ? 's' : '' }}
                            </p>
                        </div>
                        <!-- Botao para fechar o modal -->
                        <button @click="$emit('close')"
                            class="p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-400 dark:text-slate-500 transition"
                            aria-label="Fechar calendário">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Link para adicionar uma nova tarefa pre-preenchida com a data selecionada -->
                    <div v-if="selectedDay" class="px-4 py-2 border-b border-gray-100 dark:border-slate-700">
                        <a :href="`/todo/nova?date=${selectedDayIso}`"
                            class="flex items-center gap-2 py-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition text-sm font-medium">
                            <span class="h-5 w-5 flex items-center justify-center rounded-full border-2 border-blue-400 text-blue-400 text-sm leading-none font-bold shrink-0">+</span>
                            Adicionar tarefa para este dia
                        </a>
                    </div>

                    <!-- Lista de tarefas do dia selecionado -->
                    <div class="flex-1 overflow-y-auto">
                        <!-- Estado vazio: nenhum dia selecionado -->
                        <div v-if="!selectedDay" class="flex flex-col items-center justify-center gap-2 py-12 text-gray-300 dark:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-xs">Clique num dia para ver as tarefas</p>
                        </div>

                        <!-- Estado vazio: dia selecionado sem tarefas -->
                        <div v-else-if="tasksOnDay(selectedDay).length === 0"
                            class="flex flex-col items-center justify-center gap-2 py-12 text-gray-300 dark:text-slate-600">
                            <p class="text-xs">Nenhuma tarefa para este dia</p>
                        </div>

                        <!-- Lista de tarefas do dia selecionado -->
                        <ul v-else class="py-2">
                            <li v-for="task in tasksOnDay(selectedDay)" :key="task.id"
                                class="flex items-start gap-3 px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                                <!-- Ponto de cor indicando a prioridade da tarefa -->
                                <span :class="['mt-1.5 h-2 w-2 rounded-full shrink-0', priorityDot(task.priority)]"></span>
                                <div class="flex-1 min-w-0">
                                    <!-- Titulo da tarefa: riscado se estiver concluida -->
                                    <p :class="['text-sm font-medium truncate', task.status === 'concluida' ? 'line-through text-gray-400 dark:text-slate-500' : 'text-gray-800 dark:text-slate-200']">
                                        {{ task.title }}
                                    </p>
                                    <!-- Descricao resumida da tarefa (opcional) -->
                                    <p v-if="task.description" class="text-xs text-gray-400 dark:text-slate-500 truncate mt-0.5">
                                        {{ task.description }}
                                    </p>
                                    <!-- Badge de estado: verde para concluida, amarelo para pendente -->
                                    <span :class="['inline-block text-xs mt-1 px-1.5 py-0.5 rounded font-medium', task.status === 'concluida' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400']">
                                        {{ task.status === 'concluida' ? 'Concluída' : 'Pendente' }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

// Props recebidas pelo componente pai:
// - open: controla a visibilidade do modal
// - tasks: lista de todas as tarefas do utilizador
const props = defineProps({
    open: Boolean,
    tasks: Array,
});
// Evento emitido para o pai fechar o modal
defineEmits(['close']);

// Data atual usada como referencia para destacar o dia de hoje
const today = new Date();

// Estado reativo do mes e ano atualmente visualizados no calendario
const currentYear = ref(today.getFullYear());
const currentMonth = ref(today.getMonth()); // 0-indexed (0 = Janeiro, 11 = Dezembro)

// Dia atualmente selecionado no calendario (inicia com o dia de hoje)
const selectedDay = ref(today.getDate());

// Quando o modal abre, repoe o calendario no mes atual com o dia de hoje selecionado
watch(() => props.open, (val) => {
    if (val) {
        currentYear.value = today.getFullYear();
        currentMonth.value = today.getMonth();
        selectedDay.value = today.getDate();
    }
});

// Abreviaturas dos dias da semana para os cabecalhos da grelha
const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

// Label formatado do mes atual em portugues (ex: "maio de 2026")
const monthLabel = computed(() => {
    return new Date(currentYear.value, currentMonth.value, 1)
        .toLocaleDateString('pt-PT', { month: 'long', year: 'numeric' });
});

// Numero total de dias no mes atual
const daysInMonth = computed(() => {
    return new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
});

// Indice do dia da semana em que o mes comeca (0 = Domingo), usado para as celulas vazias iniciais
const firstDayOfWeek = computed(() => {
    return new Date(currentYear.value, currentMonth.value, 1).getDay();
});

// Data ISO (YYYY-MM-DD) do dia selecionado, usada para filtrar tarefas e pre-preencher o formulario
const selectedDayIso = computed(() => {
    if (!selectedDay.value) return '';
    const m = String(currentMonth.value + 1).padStart(2, '0');
    const d = String(selectedDay.value).padStart(2, '0');
    return `${currentYear.value}-${m}-${d}`;
});

// Label formatado do dia selecionado em portugues (ex: "domingo, 4 de maio")
const selectedDayLabel = computed(() => {
    if (!selectedDay.value) return '';
    return new Date(currentYear.value, currentMonth.value, selectedDay.value)
        .toLocaleDateString('pt-PT', { weekday: 'long', day: 'numeric', month: 'long' });
});

// Navega para o mes anterior; se for janeiro, volta para dezembro do ano anterior
function prevMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
    // Limpa o dia selecionado ao mudar de mes
    selectedDay.value = null;
}

// Navega para o proximo mes; se for dezembro, avanca para janeiro do ano seguinte
function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
    // Limpa o dia selecionado ao mudar de mes
    selectedDay.value = null;
}

// Define o dia selecionado ao clicar numa celula do calendario
function selectDay(day) {
    selectedDay.value = day;
}

// Verifica se o dia passado corresponde ao dia de hoje (ano, mes e dia identicos)
function isToday(day) {
    return day === today.getDate()
        && currentMonth.value === today.getMonth()
        && currentYear.value === today.getFullYear();
}

// Retorna as tarefas cuja due_date corresponde ao dia passado no mes/ano atual
function tasksOnDay(day) {
    if (!day || !props.tasks) return [];
    const m = String(currentMonth.value + 1).padStart(2, '0');
    const d = String(day).padStart(2, '0');
    const iso = `${currentYear.value}-${m}-${d}`;
    return props.tasks.filter(t => t.due_date === iso);
}

// Retorna a classe CSS da cor do ponto de prioridade consoante o valor recebido
function priorityDot(priority) {
    if (priority === 'alta') return 'bg-red-400';
    if (priority === 'media') return 'bg-amber-400';
    return 'bg-blue-400'; // baixa
}
</script>

<style scoped>
/* Animacao de entrada e saida do modal com fade */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
