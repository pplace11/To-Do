<template>
    <!-- Página de registo de novo utilizador -->
    <!-- Estrutura de duas colunas: coluna direita com formulário e coluna esquerda com painel de marketing -->
    <!-- Suporta animação de saída controlada por `isLeaving` (translação vertical + opacidade + blur) -->

    <!-- Contentor principal: gradiente de fundo multi-camada em modo claro e escuro -->
    <!-- Classes dinâmicas aplicam a animação de saída quando `isLeaving` é true -->
    <div :class="[
        'relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(96,165,250,0.35),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(129,140,248,0.28),_transparent_30%),linear-gradient(135deg,_#eef4ff_0%,_#dbeafe_45%,_#eff6ff_100%)] text-slate-900 transition-all duration-500 ease-out dark:bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.22),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(129,140,248,0.18),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_55%,_#111827_100%)] dark:text-slate-100',
        isLeaving ? '-translate-y-2 opacity-0 blur-[2px]' : 'translate-y-0 opacity-100 blur-0'
    ]">
        <!-- Elementos decorativos de fundo: blobs desfocados nos cantos, não interativos (pointer-events-none) -->
        <div class="pointer-events-none absolute inset-0 opacity-40 dark:opacity-20">
            <div class="absolute left-[-6rem] top-[-5rem] h-56 w-56 rounded-full bg-cyan-300 blur-3xl"></div>
            <div class="absolute bottom-[-8rem] right-[-4rem] h-72 w-72 rounded-full bg-indigo-300 blur-3xl"></div>
        </div>

        <!-- Contentor central: centra verticalmente o cartão em toda a altura do ecrã -->
        <main class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">

            <!-- Cartão principal: grelha de duas colunas em ecrãs grandes, glassmorphism com backdrop-blur -->
            <!-- `aria-labelledby` liga o cartão ao título h1 para acessibilidade -->
            <!-- Animação de escala aplicada durante a saída -->
            <section :class="[
                'grid w-full overflow-hidden rounded-[2rem] border border-white/60 bg-white/75 shadow-[0_30px_80px_rgba(15,23,42,0.18)] backdrop-blur-xl transition-all duration-500 ease-out dark:border-white/10 dark:bg-slate-950/65 dark:shadow-[0_30px_80px_rgba(2,6,23,0.55)] lg:grid-cols-[1.05fr_0.95fr]',
                isLeaving ? 'scale-[0.985]' : 'scale-100'
            ]" aria-labelledby="register-title">

                <!-- ── COLUNA DO FORMULÁRIO (order-1 em mobile, order-2 em desktop) ── -->
                <!-- Animação de translação horizontal durante a saída -->
                <div :class="[
                    'order-1 flex flex-col justify-center px-6 py-8 transition-all duration-500 ease-out sm:px-10 lg:order-2 lg:px-14 lg:py-14',
                    isLeaving ? 'translate-x-10 opacity-0' : 'translate-x-0 opacity-100'
                ]">
                    <!-- Logo / link para a página inicial -->
                    <a href="/" class="mx-auto inline-flex w-fit items-center gap-3 rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:text-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:bg-slate-900/80 dark:text-slate-200 dark:hover:text-blue-300" aria-label="Voltar para a pagina inicial">
                        <img :src="'/image/logo/ToDo.png'" alt="To-Do App" class="h-10 sm:h-14 lg:h-16 w-auto">
                    </a>

                    <!-- Cabeçalho textual do formulário -->
                    <div class="mt-6 sm:mt-8 max-w-md">
                        <!-- Label superior em maiúsculas -->
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-blue-600 dark:text-blue-300">Criar acesso</p>
                        <!-- Título principal — referenciado pelo aria-labelledby da section -->
                        <h1 id="register-title" class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl lg:text-5xl">Crie a sua conta</h1>
                        <!-- Subtítulo descritivo do propósito da aplicação -->
                        <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-300 sm:text-base">
                            Crie o seu acesso para gerir tarefas, definir prioridades e acompanhar entregas num unico painel.
                        </p>
                    </div>

                    <!-- Formulário de registo — @submit.prevent evita reload e chama submitRegister() -->
                    <form @submit.prevent="submitRegister" class="mt-8 space-y-5">

                        <!-- Campo de nome completo — ligado a `name` via v-model -->
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Nome</label>
                            <div class="group relative">
                                <!-- Ícone de utilizador — muda de cor quando o campo está em foco (group-focus-within) -->
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-blue-500 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.813-1.7a.75.75 0 0 1-.436-.695Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <input
                                    id="name"
                                    v-model="name"
                                    type="text"
                                    required
                                    autocomplete="name"
                                    class="w-full rounded-2xl border border-slate-200 bg-white/85 py-3 pl-12 pr-4 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Seu nome completo">
                            </div>
                        </div>

                        <!-- Campo de email — ligado a `email` via v-model -->
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Email</label>
                            <div class="group relative">
                                <!-- Ícone de envelope — muda de cor quando o campo está em foco -->
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-blue-500 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                        <path d="M1.5 8.67v8.58A2.25 2.25 0 0 0 3.75 19.5h16.5a2.25 2.25 0 0 0 2.25-2.25V8.67l-9.41 5.56a2.25 2.25 0 0 1-2.28 0L1.5 8.67Z" />
                                        <path d="M22.5 6.908V6.75A2.25 2.25 0 0 0 20.25 4.5H3.75A2.25 2.25 0 0 0 1.5 6.75v.158l10.07 5.95a.75.75 0 0 0 .86 0L22.5 6.908Z" />
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    v-model="email"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    class="w-full rounded-2xl border border-slate-200 bg-white/85 py-3 pl-12 pr-4 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="nome@empresa.com">
                            </div>
                        </div>

                        <!-- Campo de nova senha — com toggle de visibilidade -->
                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Senha</label>
                            <div class="group relative">
                                <!-- Ícone de cadeado — muda de cor quando o campo está em foco -->
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-blue-500 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12 1.5a4.5 4.5 0 0 0-4.5 4.5V9H6a2.25 2.25 0 0 0-2.25 2.25v8.25A2.25 2.25 0 0 0 6 21.75h12a2.25 2.25 0 0 0 2.25-2.25v-8.25A2.25 2.25 0 0 0 18 9h-1.5V6A4.5 4.5 0 0 0 12 1.5Zm3 7.5V6A3 3 0 0 0 9 6V9h6Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <!-- Tipo do input alterna entre 'password' e 'text' consoante `showPassword` -->
                                <input
                                    id="password"
                                    v-model="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    autocomplete="new-password"
                                    class="w-full rounded-2xl border border-slate-200 bg-white/85 py-3 pl-12 pr-14 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Crie uma senha segura">
                                <!-- Botão de toggle de visibilidade da senha -->
                                <!-- aria-pressed reflete o estado atual para leitores de ecrã -->
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 transition hover:text-blue-600 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:text-slate-400 dark:hover:text-blue-300"
                                    :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                                    :aria-pressed="showPassword"
                                    @click="showPassword = !showPassword">
                                    <!-- Ícone de olho riscado — exibido quando a senha está visível -->
                                    <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                        <path d="M3 3l18 18"></path>
                                        <path d="M10.58 10.58a2 2 0 1 0 2.83 2.83"></path>
                                        <path d="M16.68 16.69A8.7 8.7 0 0 1 12 18c-5 0-9-6-9-6a18.7 18.7 0 0 1 5.02-4.62"></path>
                                        <path d="M9.88 5.09A9.12 9.12 0 0 1 12 5c5 0 9 6 9 6a18.85 18.85 0 0 1-1.95 2.79"></path>
                                    </svg>
                                    <!-- Ícone de olho aberto — exibido quando a senha está oculta -->
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                        <path d="M1 12s4-6 11-6 11 6 11 6-4 6-11 6S1 12 1 12z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Campo de confirmação da nova senha — com toggle de visibilidade independente -->
                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Confirmar senha</label>
                            <div class="group relative">
                                <!-- Ícone de cadeado — idêntico ao campo de senha -->
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-blue-500 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12 1.5a4.5 4.5 0 0 0-4.5 4.5V9H6a2.25 2.25 0 0 0-2.25 2.25v8.25A2.25 2.25 0 0 0 6 21.75h12a2.25 2.25 0 0 0 2.25-2.25v-8.25A2.25 2.25 0 0 0 18 9h-1.5V6A4.5 4.5 0 0 0 12 1.5Zm3 7.5V6A3 3 0 0 0 9 6V9h6Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <!-- Tipo alterna entre 'password' e 'text' consoante `showPasswordConfirmation` -->
                                <input
                                    id="password_confirmation"
                                    v-model="passwordConfirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    required
                                    autocomplete="new-password"
                                    class="w-full rounded-2xl border border-slate-200 bg-white/85 py-3 pl-12 pr-14 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Repita a senha">
                                <!-- Botão de toggle de visibilidade da confirmação de senha -->
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 transition hover:text-blue-600 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:text-slate-400 dark:hover:text-blue-300"
                                    :aria-label="showPasswordConfirmation ? 'Ocultar confirmacao de senha' : 'Mostrar confirmacao de senha'"
                                    :aria-pressed="showPasswordConfirmation"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation">
                                    <!-- Ícone de olho riscado — exibido quando o campo está visível -->
                                    <svg v-if="showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                        <path d="M3 3l18 18"></path>
                                        <path d="M10.58 10.58a2 2 0 1 0 2.83 2.83"></path>
                                        <path d="M16.68 16.69A8.7 8.7 0 0 1 12 18c-5 0-9-6-9-6a18.7 18.7 0 0 1 5.02-4.62"></path>
                                        <path d="M9.88 5.09A9.12 9.12 0 0 1 12 5c5 0 9 6 9 6a18.85 18.85 0 0 1-1.95 2.79"></path>
                                    </svg>
                                    <!-- Ícone de olho aberto — exibido quando o campo está oculto -->
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                        <path d="M1 12s4-6 11-6 11 6 11 6-4 6-11 6S1 12 1 12z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Mensagem de erro: exibida quando `errorMessage` tem conteúdo -->
                        <p v-if="errorMessage" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">{{ errorMessage }}</p>

                        <!-- Botão de submissão: desativado durante o envio, texto muda para feedback visual -->
                        <!-- Gradiente azul/índigo com sombra colorida -->
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:translate-y-[-1px] hover:shadow-xl hover:shadow-blue-500/30 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-70">
                            {{ isLoading ? 'Criando...' : 'Criar conta' }}
                        </button>

                    </form>
                </div>

                <!-- ── COLUNA LATERAL / PAINEL DE MARKETING (order-2 em mobile, order-1 em desktop) ── -->
                <!-- Fundo em gradiente azul/índigo; animação de translação oposta à coluna do formulário -->
                <div :class="[
                    'order-2 flex items-center bg-gradient-to-br from-blue-500 via-indigo-500 to-blue-700 px-6 py-8 text-white transition-all duration-500 ease-out lg:order-1 lg:px-14 lg:py-14',
                    isLeaving ? '-translate-x-10 opacity-0' : 'translate-x-0 opacity-100'
                ]">
                    <div class="flex w-full justify-center">
                        <!-- Cartão glassmorphism com conteúdo de marketing -->
                        <div class="relative max-w-md rounded-[2rem] border border-white/20 bg-white/10 p-6 shadow-[inset_0_1px_0_rgba(255,255,255,0.18)] backdrop-blur-sm sm:p-8">

                            <!-- Botão de fechar / voltar à página inicial -->
                            <!-- Em desktop posiciona-se fora do cartão (lg:-left-5 lg:-top-11) -->
                            <button
                                type="button"
                                class="absolute right-3 top-3 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/50 bg-white/15 text-white shadow-lg backdrop-blur transition hover:scale-105 hover:bg-white/25 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-white/50 lg:-left-5 lg:right-auto lg:-top-11"
                                aria-label="Fechar pagina"
                                title="Fechar"
                                @click="navigateWithTransition('/')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                    <path d="M6 6l12 12"></path>
                                    <path d="M18 6l-12 12"></path>
                                </svg>
                            </button>

                            <!-- Identificador da aplicação em maiúsculas -->
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-100">To-Do App</p>
                            <!-- Título do painel de marketing -->
                            <h2 class="mt-4 text-2xl font-black tracking-tight sm:text-3xl lg:text-4xl">Retome as suas tarefas</h2>
                            <!-- Texto descritivo — oculto em ecrãs muito pequenos (hidden sm:block) -->
                            <p class="mt-3 text-sm leading-6 text-blue-50/90 hidden sm:block">
                                Ja possui conta? Entre para continuar a acompanhar listas, prioridades e o progresso do seu trabalho.
                            </p>

                            <!-- Cards de funcionalidades em grelha de 2 colunas -->
                            <div class="mt-4 sm:mt-8 grid grid-cols-2 gap-3 text-sm text-blue-50/90">
                                <div class="rounded-2xl bg-white/10 px-4 py-3">
                                    <span class="block text-2xl font-black text-white">Tarefas</span>
                                    <span>Visualize o que precisa fazer</span>
                                </div>
                                <div class="rounded-2xl bg-white/10 px-4 py-3">
                                    <span class="block text-2xl font-black text-white">Progresso</span>
                                    <span>Mantenha o foco nas entregas</span>
                                </div>
                            </div>

                            <!-- Link para a página de login com animação de transição -->
                            <!-- @click.prevent substitui a navegação nativa pela transição suave -->
                            <a
                                href="/login"
                                @click.prevent="navigateWithTransition('/login')"
                                class="mt-8 inline-flex w-full items-center justify-center rounded-2xl border border-white/60 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-white/50 sm:w-auto">
                                Login
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Botão flutuante de alternância de tema (claro/escuro) -->
        <!-- Posicionado de forma fixa no canto inferior direito (bottom-4 right-4) com z-index alto -->
        <!-- aria-pressed reflete o estado atual do tema para leitores de ecrã -->
        <button
            id="theme-toggle"
            type="button"
            class="fixed bottom-4 right-4 z-50 rounded-full border border-white/70 bg-white/85 p-3 text-slate-700 shadow-lg backdrop-blur transition hover:scale-105 hover:bg-white focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-700 dark:bg-slate-900/85 dark:text-slate-100 dark:hover:bg-slate-900"
            :aria-label="themeLabel"
            :aria-pressed="isDark"
            title="Alternar tema"
            @click="toggleTheme">
            <span class="sr-only">{{ themeLabel }}</span>
            <!-- Ícone de sol — visível no modo claro -->
            <svg v-show="!isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
            </svg>
            <!-- Ícone de lua — visível no modo escuro -->
            <svg v-show="isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                <path d="M21 12.79A9 9 0 1 1 11.21 3c0.25 0 0.5 0.01 0.75 0.04A7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>
    </div>
</template>

<script setup>
// Importações do Vue:
// - computed: para o label dinâmico do botão de tema
// - onMounted / onUnmounted: para registar/desregistar o listener de storage
// - ref: para todas as variáveis reativas do componente
import { computed, onMounted, onUnmounted, ref } from 'vue';

// Campos do formulário de registo — ligados aos inputs via v-model
const name = ref('');                        // Nome completo do utilizador
const email = ref('');                       // Email do utilizador
const password = ref('');                    // Nova senha
const passwordConfirmation = ref('');        // Confirmação da nova senha

// Estado de UI
const isLoading = ref(false);               // true durante o pedido POST /register (bloqueia o botão)
const errorMessage = ref('');               // Mensagem de erro exibida abaixo dos campos
const isDark = ref(false);                  // Estado atual do tema (true = modo escuro)
const isLeaving = ref(false);               // true quando a animação de saída está a decorrer

// Visibilidade dos campos de senha
const showPassword = ref(false);            // true = campo de senha exibe texto simples
const showPasswordConfirmation = ref(false);// true = campo de confirmação exibe texto simples

// Token CSRF lido do meta tag do HTML — incluído nos cabeçalhos dos pedidos POST
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Label acessível do botão de tema — atualizado automaticamente quando `isDark` muda
const themeLabel = computed(() => (isDark.value ? 'Ativar tema claro' : 'Ativar tema escuro'));

// Sincroniza `isDark` com a classe 'dark' presente no elemento <html>
// Chamado na montagem e sempre que o localStorage for alterado noutro separador
function syncThemeState() {
    isDark.value = document.documentElement.classList.contains('dark');
}

// Listener do evento 'storage' — reage a alterações de tema feitas noutros separadores do browser
function handleStorage(event) {
    if (event.key === 'theme') {
        syncThemeState();
    }
}

// Alterna o tema claro/escuro:
// 1. Adiciona/remove a classe 'dark' no <html>
// 2. Persiste a preferência no localStorage
// 3. Atualiza `isDark` para refletir o novo estado
function toggleTheme() {
    const dark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    isDark.value = dark;
}

// Inicia a animação de saída e navega para `url` após 360 ms
// O guarda `isLeaving.value` evita chamadas duplas durante a transição
function navigateWithTransition(url) {
    if (isLeaving.value) {
        return;
    }

    isLeaving.value = true;
    window.setTimeout(() => {
        window.location.href = url;
    }, 360);
}

// Envia os dados de registo para o servidor via POST /register
// Em caso de sucesso, redireciona para `data.redirect` ou '/todo'
// Em caso de erro, exibe a primeira mensagem de validação ou mensagem genérica
async function submitRegister() {
    isLoading.value = true;    // Bloqueia o botão durante o envio
    errorMessage.value = '';   // Limpa erros anteriores

    try {
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}), // Inclui CSRF token se disponível
            },
            body: JSON.stringify({
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation: passwordConfirmation.value,
            }),
        });

        const data = await response.json().catch(() => ({})); // Fallback para objeto vazio se o corpo não for JSON

        if (response.ok) {
            // Registo bem-sucedido: redireciona para a rota indicada pelo servidor ou para '/todo'
            window.location.href = data?.redirect || '/todo';
            return;
        }

        // Erro de validação ou outro erro: exibe a mensagem do servidor ou o primeiro erro de campo
        errorMessage.value = data?.message || Object.values(data?.errors || {}).flat()[0] || 'Nao foi possivel criar conta.';
    } catch {
        // Erro de rede ou exceção inesperada
        errorMessage.value = 'Erro de conexao. Tente novamente.';
    } finally {
        isLoading.value = false; // Liberta o botão independentemente do resultado
    }
}

// Ciclo de vida: após a montagem, sincroniza o tema e regista o listener de storage
onMounted(() => {
    syncThemeState();
    window.addEventListener('storage', handleStorage);
});

// Ciclo de vida: antes da destruição do componente, remove o listener para evitar fugas de memória
onUnmounted(() => {
    window.removeEventListener('storage', handleStorage);
});
</script>
