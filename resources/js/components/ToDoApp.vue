<template>
    <!-- Componente raiz da aplicação To-Do -->
    <!-- Responsável pelo layout principal: sidebar de navegação + área de conteúdo -->
    <!-- Gere o estado global das tarefas, filtros, modais, notificações e autenticação -->
    <!-- Layout: flex h-screen com overflow-hidden para evitar scroll duplo -->
    <!-- Fundo dark: gradiente radial customizado centrado no topo -->
    <div class="flex h-screen bg-white text-gray-800 dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100 overflow-hidden">

        <!-- ===== SKIP LINK (ACESSIBILIDADE) ===== -->
        <!-- Oculto visualmente (sr-only) mas acessível via teclado (focus:not-sr-only) -->
        <!-- Permite que utilizadores de teclado/leitor de ecrã saltem a sidebar e ir direto ao conteúdo -->
        <a href="#todo-main"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:outline-none focus:ring-4 focus:ring-blue-300">
            Pular para o conteúdo principal
        </a>

        <!-- ===== BACKDROP DA SIDEBAR (MOBILE) ===== -->
        <!-- Sobreposição semitransparente exibida em mobile quando a sidebar está aberta -->
        <!-- Clicar no backdrop fecha a sidebar; aria-hidden porque é puramente decorativo -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-20 bg-black/40 md:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

        <!-- ===== SIDEBAR ===== -->
        <!-- Em mobile: posição fixa, desliza da esquerda com translate-x (controlled by sidebarOpen) -->
        <!-- Em desktop (md+): estática no fluxo, sempre visível (md:translate-x-0, md:transition-none) -->
        <!-- z-30 garante que a sidebar fique acima do backdrop (z-20) -->
        <aside :class="[
            'flex flex-col bg-gray-50 dark:bg-transparent border-r border-gray-200 dark:border-slate-700 pb-3',
            'w-64 shrink-0 fixed inset-y-0 left-0 z-30',
            'transition-transform duration-200 ease-in-out',
            'md:static md:inset-auto md:z-auto md:translate-x-0 md:transition-none',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]">
            <!-- Logótipo da aplicação -->
            <div class="flex items-center h-16 px-4 gap-2 border-b border-gray-200 dark:border-slate-700">
                <img :src="'/image/logo/ToDo.png'" alt="To-Do" class="h-12 w-auto" />
            </div>

            <!-- Navegação principal: lista de vistas disponíveis -->
            <!-- aria-label identifica semanticamente a região de navegação para leitores de ecrã -->
            <nav class="flex-1 px-2 py-2 space-y-0.5" aria-label="Navegação principal">
                <!-- Botão de navegação para cada item gerado por navItems (computed) -->
                <!-- aria-current="page" assinala a vista ativa para tecnologias assistivas -->
                <!-- Fundo e cor mudам conforme o item está ativo ou não -->
                <button v-for="item in navItems" :key="item.key"
                    @click="activateView(item.key)"
                    :aria-current="sidebarView === item.key ? 'page' : undefined"
                    :class="[
                        'w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                        sidebarView === item.key
                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                            : 'text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700'
                    ]">
                    <!-- Ícone SVG injetado como HTML (v-html) — aria-hidden evita leitura duplicada -->
                    <span class="shrink-0" aria-hidden="true" v-html="item.icon"></span>
                    <span>{{ item.label }}</span>
                    <!-- Contador de tarefas relevantes — só exibido quando count > 0 -->
                    <!-- tabular-nums: garante alinhamento consistente dos dígitos -->
                    <span v-if="item.count > 0" class="ml-auto text-xs text-gray-400 dark:text-slate-500 tabular-nums">{{ item.count }}</span>
                </button>
            </nav>

            <!-- Atalho para criação rápida de tarefa (navega para /todo/nova) -->
            <div class="px-2 pb-2 border-t border-gray-200 dark:border-slate-700 pt-2">
                <a href="/todo/nova"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition font-medium">
                    <!-- Ícone de "+" — aria-hidden porque o texto "Nova tarefa" já descreve a ação -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova tarefa
                </a>
            </div>

            <!-- Botões de ação no rodapé da sidebar: calendário, partilhar e sair -->
            <div class="flex items-center justify-around px-3 py-3 border-t border-gray-200 dark:border-slate-700">

                <!-- Botão Calendário: abre o componente CalendarView via showCalendar -->
                <!-- Fundo ativo quando o calendário está aberto -->
                <button @click="showCalendar = true" :class="['p-2 rounded-lg transition', showCalendar ? 'bg-gray-200 dark:bg-slate-700' : 'hover:bg-gray-200 dark:hover:bg-slate-700']" aria-label="Calendário">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>

                <!-- Botão Partilhar: abre o componente ShareTaskModal via showShareModal -->
                <button @click="showShareModal = true" :class="['p-2 rounded-lg transition', showShareModal ? 'bg-gray-200 dark:bg-slate-700' : 'hover:bg-gray-200 dark:hover:bg-slate-700']" aria-label="Partilhar tarefa">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5.196-3.796M9 20H4v-2a4 4 0 015.196-3.796M15 7a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>

                <!-- Botão Sair: chama logout() que faz POST /logout e redireciona para "/" -->
                <button @click="logout" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition" aria-label="Sair">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </aside>

        <!-- ===== ÁREA DE CONTEÚDO PRINCIPAL ===== -->
        <!-- flex-1: ocupa o espaço restante após a sidebar -->
        <!-- overflow-hidden: necessário para que o scroll fique apenas no <main> interior -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- ===== BARRA SUPERIOR (HEADER) ===== -->
            <!-- flex-wrap: permite que os controles da direita desçam para segunda linha em mobile -->
            <!-- shrink-0: impede que o header encolha quando o conteúdo é longo -->
            <header class="flex flex-wrap items-center justify-between gap-x-2 gap-y-1.5 px-3 sm:px-6 py-2 sm:py-3 border-b border-gray-200 dark:border-slate-700 bg-white dark:bg-transparent shrink-0 min-h-[52px]">

                <!-- Grupo esquerdo: hamburger + título + toggles de vista -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 w-full sm:flex-1 sm:w-auto">

                    <!-- Botão hamburger — só visível em mobile (md:hidden) -->
                    <!-- aria-expanded comunica o estado aberto/fechado a leitores de ecrã -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition shrink-0"
                        :aria-label="sidebarOpen ? 'Fechar menu' : 'Abrir menu'"
                        :aria-expanded="sidebarOpen">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div>
                        <!-- Contentor do título e menu de opções (posicionamento relativo para o dropdown) -->
                        <!-- ref="optionsMenuRef" usado por handleDocumentClick para fechar o menu ao clicar fora -->
                        <div ref="optionsMenuRef" class="relative flex items-center gap-1.5">

                            <!-- Título da vista atual (computed activeViewLabel) -->
                            <h1 id="todo-page-title" class="text-xl font-semibold text-gray-800 dark:text-slate-100">{{ activeViewLabel }}</h1>

                            <!-- Botão de três pontos para o menu de opções -->
                            <!-- aria-expanded comunica o estado aberto/fechado -->
                            <button @click="showOptionsMenu = !showOptionsMenu" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-slate-700 transition" aria-label="Mais opções" :aria-expanded="showOptionsMenu">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-slate-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <circle cx="4" cy="10" r="1.5"/><circle cx="10" cy="10" r="1.5"/><circle cx="16" cy="10" r="1.5"/>
                                </svg>
                            </button>

                            <!-- Dropdown de opções — visível apenas quando showOptionsMenu === true -->
                            <!-- Posicionado absolutamente abaixo do botão de três pontos (top-full) -->
                            <div v-if="showOptionsMenu" class="absolute left-0 top-full z-30 mt-2 w-72 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                                <!-- Link para a página de perfil -->
                                <a href="/perfil" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <span>Perfil</span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Nome, email e senha</span>
                                </a>
                                <!-- Botão para ativar/desativar autenticação de dois fatores -->
                                <!-- O texto e a cor do indicador refletem o estado atual de two_factor_enabled -->
                                <button @click="toggleTwoFactorFromMenu" class="mt-1 flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <span>{{ currentUser.two_factor_enabled ? 'Desativar dois fatores' : 'Ativar dois fatores' }}</span>
                                    <span :class="['text-xs font-semibold', currentUser.two_factor_enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500']">
                                        {{ currentUser.two_factor_enabled ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Data formatada em português — oculta em mobile (hidden sm:block) -->
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5 capitalize hidden sm:block">{{ todayFormatted }}</p>
                    </div>

                    <!-- Abas de alternância de vista: Grelha / Lista -->
                    <!-- border-b-2 colorida indica a aba ativa; border-transparent nas inativas -->
                    <div class="flex items-center gap-0.5 pb-0.5 shrink-0">
                        <!-- Aba Grelha -->
                        <button
                            @click="viewMode = 'grelha'"
                            :class="['flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium border-b-2 transition', viewMode === 'grelha' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300']">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z" />
                            </svg>
                            <!-- Texto "Grelha" oculto em mobile para poupar espaço -->
                            <span class="hidden sm:inline">Grelha</span>
                        </button>
                        <!-- Aba Lista -->
                        <button
                            @click="viewMode = 'lista'"
                            :class="['flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium border-b-2 transition', viewMode === 'lista' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300']">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <!-- Texto "Lista" oculto em mobile para poupar espaço -->
                            <span class="hidden sm:inline">Lista</span>
                        </button>
                    </div>
                </div>

                <!-- Grupo direito: selects de ordenação e filtragem por prioridade -->
                <!-- Em mobile: ocupa a linha toda (w-full); em sm+: tamanho automático (sm:w-auto) -->
                <div class="flex items-center gap-1 w-full sm:w-auto sm:shrink-0">

                    <!-- Select de ordenação: ascendente (datas mais próximas) ou descendente -->
                    <!-- label com sr-only garante acessibilidade sem ocupar espaço visual -->
                    <label for="sort-order" class="sr-only">Ordenação</label>
                    <select v-model="sortOrder" id="sort-order"
                        class="flex-1 sm:flex-none text-sm text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900/30 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/70 focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer">
                        <option value="asc">↑↓ Mais próximas</option>
                        <option value="desc">↑↓ Mais distantes</option>
                    </select>

                    <!-- Select de filtro por prioridade: "" = sem filtro (todas), ou 'alta'/'media'/'baixa' -->
                    <label for="filter-priority" class="sr-only">Prioridade</label>
                    <select v-model="filterPriority" id="filter-priority"
                        class="flex-1 sm:flex-none text-sm text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900/30 rounded-md px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/70 focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer">
                        <option value="">Todas prioridades</option>
                        <option value="alta">Alta</option>
                        <option value="media">Média</option>
                        <option value="baixa">Baixa</option>
                    </select>
                </div>
            </header>

            <!-- ===== CONTEÚDO PRINCIPAL COM SCROLL ===== -->
            <!-- id="todo-main" é o alvo do skip link no topo -->
            <!-- tabindex="-1": permite receber foco programático (via skip link) sem aparecer no tab order -->
            <main id="todo-main" tabindex="-1" class="flex-1 overflow-y-auto bg-white dark:bg-transparent">

                <!-- Faixa de estatísticas: total, pendentes e concluídas -->
                <div class="flex items-center justify-center gap-6 px-6 py-3 border-b border-gray-100 dark:border-slate-800 text-sm">
                    <span class="text-gray-500 dark:text-slate-400"><span class="font-semibold text-gray-700 dark:text-slate-200">{{ totalTasks }}</span> total</span>
                    <span class="text-amber-600"><span class="font-semibold">{{ pendingTasks }}</span> pendentes</span>
                    <span class="text-emerald-600"><span class="font-semibold">{{ completedTasks }}</span> concluídas</span>
                </div>

                <!-- Atalho de adição rápida de tarefa (navega para /todo/nova) -->
                <div class="px-6 py-1 border-b border-gray-100 dark:border-slate-800">
                    <a href="/todo/nova"
                        class="flex items-center gap-2 py-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition text-sm font-medium">
                        <!-- Ícone "+" circular decorativo -->
                        <span class="h-5 w-5 flex items-center justify-center rounded-full border-2 border-blue-400 text-blue-400 text-sm leading-none font-bold shrink-0">+</span>
                        Adicionar uma tarefa
                    </a>
                </div>

                <!-- Indicador de carregamento — visível enquanto loading === true -->
                <!-- role="status" + aria-live="polite": anuncia ao leitor de ecrã que os dados estão a carregar -->
                <div v-if="loading" class="flex flex-col items-center justify-center gap-3 py-20 text-gray-400 dark:text-slate-500" role="status" aria-live="polite">
                    <!-- animate-spin: classe Tailwind que aplica rotação contínua CSS ao ícone de spinner -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-sm">Carregando tarefas...</span>
                </div>

                <!-- Lista/grelha de tarefas — exibida após o carregamento -->
                <!-- Passa a lista filtrada/ordenada (filteredTasks) e o modo de visualização -->
                <!-- Escuta três eventos do TaskList: editar, concluir e eliminar tarefa -->
                <TaskList v-else :tasks="filteredTasks" :view-mode="viewMode" @edit="openEditModal" @complete="completeTask" @delete="deleteTask" />
            </main>
        </div>

        <!-- ===== COMPONENTES GLOBAIS / MODAIS ===== -->

        <!-- Toast de notificação global — posicionado fixed pelo próprio componente -->
        <!-- Recebe tipo ('success'|'error'|'info') e texto da mensagem -->
        <NotificationToast :type="notificationType" :text="notificationText" />

        <!-- Modal de edição de tarefa — controlado via ref (editModal.open() / editModal.close()) -->
        <!-- Recebe a tarefa a editar e emite 'save' com os dados atualizados -->
        <TaskEditModal ref="editModal" :task="editingTask" @save="saveTask" />

        <!-- Componente de calendário — abre/fecha via prop :open -->
        <!-- Recebe todas as tarefas para visualização por data -->
        <CalendarView :open="showCalendar" :tasks="tasks" @close="showCalendar = false" />

        <!-- Modal de partilha de tarefa — abre/fecha via prop :open -->
        <!-- Emite 'share' com { taskId, email } para enviar ao backend -->
        <ShareTaskModal :open="showShareModal" :tasks="tasks" @close="showShareModal = false" @share="shareTask" />
    </div>
</template>

<script setup>
// Importações do Vue:
// - ref: variáveis reativas simples
// - computed: propriedades derivadas do estado reativo
// - onMounted / onUnmounted: ciclo de vida para registar/remover event listeners
import { ref, computed, onMounted, onUnmounted } from 'vue';

// Componentes filhos utilizados no template
import TaskList from './TaskList.vue';           // Lista/grelha de tarefas com ações
import NotificationToast from './NotificationToast.vue'; // Toast de notificação global
import TaskEditModal from './TaskEditModal.vue'; // Modal de edição com focus trap
import CalendarView from './CalendarView.vue';   // Vista de calendário com tarefas
import ShareTaskModal from './ShareTaskModal.vue'; // Modal de partilha por email

// ===== ESTADO REATIVO =====

const tasks = ref([]);              // Array com todas as tarefas carregadas da API
const loading = ref(false);         // Controla a exibição do spinner de carregamento
const filterStatus = ref('');       // Filtro de estado: '' | 'pendente' | 'concluida'
const filterPriority = ref('');     // Filtro de prioridade: '' | 'alta' | 'media' | 'baixa'
const sortOrder = ref('asc');       // Ordenação por data: 'asc' (mais próxima) | 'desc' (mais distante)
const editingTask = ref(null);      // Tarefa atualmente selecionada para edição no modal
const editModal = ref(null);        // Referência ao componente TaskEditModal para chamar open()/close()
const showCalendar = ref(false);    // Controla a visibilidade do componente CalendarView
const showShareModal = ref(false);  // Controla a visibilidade do componente ShareTaskModal
const showOptionsMenu = ref(false); // Controla a visibilidade do dropdown de opções (três pontos)
const sidebarView = ref('todas');   // Vista ativa na sidebar: 'hoje'|'importante'|'planeado'|'pendentes'|'todas'
const sidebarOpen = ref(false);     // Estado da sidebar em mobile (drawer aberto/fechado)
const viewMode = ref('lista');      // Modo de visualização das tarefas: 'lista' | 'grelha'
const isDark = ref(document.documentElement.classList.contains('dark')); // Estado inicial do tema
const optionsMenuRef = ref(null);   // Referência ao contentor do menu de opções para detetar cliques fora

// Estado do utilizador atual — carregado via fetchCurrentUser()
const currentUser = ref({
    name: '',
    email: '',
    two_factor_enabled: false,
});

// ===== TEMA =====

// Alterna entre modo claro e escuro
// Atualiza a classe 'dark' no <html> e persiste a preferência no localStorage
function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}

// ===== ÍCONES SVG (strings inline) =====
// Usados como v-html nos itens da sidebar via navItems computed
// Mantidos como constantes para evitar re-renderizações desnecessárias

const iconSun = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M5.22 5.22l1.42 1.42m10.72 10.72 1.42 1.42M3 12h2m14 0h2M5.22 18.78l1.42-1.42M17.36 6.64l1.42-1.42M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>`;
const iconStar = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557L3.04 10.395a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>`;
const iconCalendar = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`;
const iconHome = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/></svg>`;
const iconCheck = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;

// ===== NOTIFICAÇÕES =====

const notificationText = ref('');     // Texto da última notificação a exibir
const notificationType = ref('info'); // Tipo: 'success' | 'error' | 'info'

// Token CSRF lido da meta tag do HTML — incluído nos cabeçalhos de todas as mutações (POST/PUT/DELETE)
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content') || '';

// ===== COMPUTED =====

// Gera os itens de navegação da sidebar com ícone, label e contador de tarefas relevantes
// Recalculado automaticamente quando `tasks` ou `todayIso` mudam
const navItems = computed(() => [
    { key: 'hoje',      label: 'O Meu Dia',  icon: iconSun,      count: tasks.value.filter(t => t.due_date === todayIso.value && t.status === 'pendente').length },
    { key: 'importante', label: 'Importante', icon: iconStar,     count: tasks.value.filter(t => t.priority === 'alta' && t.status === 'pendente').length },
    { key: 'planeado',  label: 'Planeado',   icon: iconCalendar, count: tasks.value.filter(t => t.due_date).length },
    { key: 'pendentes', label: 'Pendentes',  icon: iconCheck,    count: tasks.value.filter(t => t.status === 'pendente').length },
    { key: 'todas',     label: 'Tarefas',    icon: iconHome,     count: 0 },
]);

// Data de hoje no formato ISO (YYYY-MM-DD) para comparação com due_date das tarefas
const todayIso = computed(() => new Date().toISOString().split('T')[0]);

// Data formatada em português para exibição no header (ex: "domingo, 4 de maio")
const todayFormatted = computed(() => {
    return new Date().toLocaleDateString('pt-PT', { weekday: 'long', day: 'numeric', month: 'long' });
});

// Rótulo da vista ativa para exibição no h1 do header
// Usa optional chaining + fallback para 'Tarefas' caso a chave não exista
const activeViewLabel = computed(() => {
    return navItems.value.find(n => n.key === sidebarView.value)?.label ?? 'Tarefas';
});

// ===== NAVEGAÇÃO DA SIDEBAR =====

// Ativa uma vista da sidebar e fecha o drawer em mobile
// Aplica o filtro de estado correspondente à vista selecionada
function activateView(key) {
    sidebarView.value = key;
    sidebarOpen.value = false; // Fecha o drawer em mobile após selecionar uma vista
    if (key === 'pendentes') {
        filterStatus.value = 'pendente'; // Filtro automático para a vista de pendentes
    } else {
        filterStatus.value = ''; // Sem filtro de estado para as restantes vistas
    }
}

// ===== FILTRAGEM E ORDENAÇÃO =====

// Lista de tarefas filtrada e ordenada para o componente TaskList
// Pipeline: cópia do array → filtro por vista → filtro por estado → filtro por prioridade → ordenação
const filteredTasks = computed(() => {
    let list = [...tasks.value]; // Cópia para não mutar o array original

    // Filtros da sidebar (vista ativa)
    if (sidebarView.value === 'hoje')      list = list.filter(t => t.due_date === todayIso.value);
    if (sidebarView.value === 'importante') list = list.filter(t => t.priority === 'alta');
    if (sidebarView.value === 'planeado')  list = list.filter(t => t.due_date); // Qualquer tarefa com data
    if (sidebarView.value === 'pendentes') list = list.filter(t => t.status === 'pendente');

    // Filtros do header
    if (filterStatus.value)   list = list.filter(t => t.status === filterStatus.value);
    if (filterPriority.value) list = list.filter(t => t.priority === filterPriority.value);

    // Ordenação por data de vencimento
    // Tarefas sem data ficam sempre no final (return 1 / return -1)
    list = list.sort((a, b) => {
        if (!a.due_date) return 1;
        if (!b.due_date) return -1;
        return sortOrder.value === 'asc'
            ? new Date(a.due_date) - new Date(b.due_date)   // Mais próximas primeiro
            : new Date(b.due_date) - new Date(a.due_date);  // Mais distantes primeiro
    });
    return list;
});

// Contadores para a faixa de estatísticas
const totalTasks     = computed(() => tasks.value.length);
const pendingTasks   = computed(() => tasks.value.filter(task => task.status === 'pendente').length);
const completedTasks = computed(() => tasks.value.filter(task => task.status === 'concluida').length);

// ===== NOTIFICAÇÕES =====

// Define o texto e o tipo da próxima notificação a exibir no NotificationToast
// O watcher interno do toast lida com o auto-dismiss após a duração configurada
function showNotification(message, type = 'info') {
    notificationText.value = message;
    notificationType.value = type;
}

// ===== MENU DE OPÇÕES (TRÊS PONTOS) =====

// Fecha o dropdown de opções quando o utilizador clica fora do contentor optionsMenuRef
// Registado em onMounted e removido em onUnmounted para evitar memory leaks
function handleDocumentClick(event) {
    if (!showOptionsMenu.value) {
        return; // Evita processamento desnecessário quando o menu está fechado
    }
    if (optionsMenuRef.value && !optionsMenuRef.value.contains(event.target)) {
        showOptionsMenu.value = false;
    }
}

// ===== CHAMADAS À API =====

// Carrega os dados do utilizador autenticado (nome, email, two_factor_enabled)
// Usado para mostrar o estado correto do 2FA no dropdown de opções
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
        // Em caso de erro, mantém os valores padrão para não bloquear a UI
        currentUser.value = {
            name: '',
            email: '',
            two_factor_enabled: false,
        };
    }
}

// Carrega todas as tarefas do utilizador autenticado
// Redireciona para /login se a sessão expirou (401)
async function fetchTasks() {
    loading.value = true;
    try {
        const res = await fetch('/tasks');
        if (res.status === 401) {
            window.location.href = '/login'; // Sessão expirada
            return;
        }
        if (!res.ok) throw new Error('Erro ao buscar tarefas');
        tasks.value = await res.json();
    } catch (error) {
        showNotification('Erro ao carregar tarefas', 'error');
    } finally {
        loading.value = false; // Sempre desativa o spinner, mesmo em caso de erro
    }
}

// Abre o modal de edição com a tarefa selecionada
// Guarda a tarefa em editingTask (passada como prop ao TaskEditModal)
// e chama open() no componente filho via referência
function openEditModal(task) {
    editingTask.value = task;
    editModal.value?.open(); // Optional chaining: seguro se o ref ainda não estiver montado
}

// Envia os dados atualizados da tarefa para a API via PUT
// Após sucesso, recarrega a lista e exibe notificação
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

// Marca uma tarefa como concluída via PUT com { status: 'concluida' }
// Chamado pelo evento 'complete' emitido pelo TaskList
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

// Elimina uma tarefa via DELETE após confirmação do utilizador
// confirm() bloqueia a execução até o utilizador confirmar ou cancelar
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

// Envia um pedido de partilha da tarefa com outro utilizador via POST /tasks/{id}/share
// Recebe o payload { taskId, email } emitido pelo ShareTaskModal
// Fecha o modal e exibe notificação de sucesso ou erro
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

        // .catch(() => ({})) garante que erros de parsing JSON não propagam a exceção
        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            throw new Error(data.message || 'Erro ao partilhar tarefa');
        }

        showShareModal.value = false; // Fecha o modal após partilha bem-sucedida
        showNotification(data.message || 'Tarefa partilhada com sucesso!', 'success');
    } catch (error) {
        showNotification(error.message || 'Erro ao partilhar tarefa', 'error');
    }
}

// Ativa ou desativa a autenticação de dois fatores diretamente a partir do menu de opções
// Envia o estado invertido (negação do estado atual) via PUT /profile/two-factor
// Atualiza currentUser.two_factor_enabled e fecha o dropdown após sucesso
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

        // !! converte para booleano estrito, independente do tipo devolvido pela API
        currentUser.value.two_factor_enabled = !!data.two_factor_enabled;
        showOptionsMenu.value = false; // Fecha o dropdown
        showNotification(data.message || 'Autenticação de dois fatores atualizada.', 'success');
    } catch (error) {
        showNotification(error.message || 'Erro ao atualizar autenticação de dois fatores', 'error');
    }
}

// Faz logout via POST /logout e redireciona sempre para "/" (bloco finally garante o redirect)
// O finally garante que o utilizador é redirecionado mesmo se o pedido falhar
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
        window.location.href = '/'; // Redireciona para a página inicial após logout
    }
}

// ===== CICLO DE VIDA =====

// onMounted: carrega as tarefas e o utilizador, e regista o listener global de cliques
onMounted(() => {
    fetchTasks();
    fetchCurrentUser();
    document.addEventListener('click', handleDocumentClick); // Para fechar o dropdown ao clicar fora
});

// onUnmounted: remove o listener global para evitar memory leaks
onUnmounted(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>

<style scoped></style>
