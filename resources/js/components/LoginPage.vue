<template>
    <div :class="[
        'relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(96,165,250,0.35),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(129,140,248,0.28),_transparent_30%),linear-gradient(135deg,_#eef4ff_0%,_#dbeafe_45%,_#eff6ff_100%)] text-slate-900 transition-all duration-500 ease-out dark:bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.22),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(129,140,248,0.18),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_55%,_#111827_100%)] dark:text-slate-100',
        isLeaving ? '-translate-y-2 opacity-0 blur-[2px]' : 'translate-y-0 opacity-100 blur-0'
    ]">
        <div class="pointer-events-none absolute inset-0 opacity-40 dark:opacity-20">
            <div class="absolute left-[-6rem] top-[-5rem] h-56 w-56 rounded-full bg-cyan-300 blur-3xl"></div>
            <div class="absolute bottom-[-8rem] right-[-4rem] h-72 w-72 rounded-full bg-indigo-300 blur-3xl"></div>
        </div>

        <main class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
            <section :class="[
                'grid w-full overflow-hidden rounded-[2rem] border border-white/60 bg-white/75 shadow-[0_30px_80px_rgba(15,23,42,0.18)] backdrop-blur-xl transition-all duration-500 ease-out dark:border-white/10 dark:bg-slate-950/65 dark:shadow-[0_30px_80px_rgba(2,6,23,0.55)] lg:grid-cols-[1.05fr_0.95fr]',
                isLeaving ? 'scale-[0.985]' : 'scale-100'
            ]" aria-labelledby="login-title">
                <div :class="[
                    'order-1 flex flex-col justify-center px-6 py-8 transition-all duration-500 ease-out sm:px-10 lg:order-1 lg:px-14 lg:py-14',
                    isLeaving ? '-translate-x-10 opacity-0' : 'translate-x-0 opacity-100'
                ]">
                    <a href="/" class="mx-auto inline-flex w-fit items-center gap-3 rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:text-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:bg-slate-900/80 dark:text-slate-200 dark:hover:text-blue-300" aria-label="Voltar para a pagina inicial">
                        <img :src="'/image/logo/ToDo.png'" alt="To-Do App" class="h-10 sm:h-14 lg:h-16 w-auto">
                    </a>

                    <div class="mt-6 sm:mt-8 max-w-md">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-blue-600 dark:text-blue-300">Acesso ao To-Do App</p>
                        <h1 id="login-title" class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl lg:text-5xl">Bem-vindo de volta</h1>
                        <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-300 sm:text-base">
                            Entre para gerir tarefas, acompanhar prioridades e manter o trabalho da equipa sempre organizado.
                        </p>
                    </div>

                    <form @submit.prevent="pendingTwoFactor ? submitTwoFactor() : submitLogin()" class="mt-8 space-y-5">
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Email</label>
                            <div class="group relative">
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

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Senha</label>
                            <div class="group relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition group-focus-within:text-blue-500 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12 1.5a4.5 4.5 0 0 0-4.5 4.5V9H6a2.25 2.25 0 0 0-2.25 2.25v8.25A2.25 2.25 0 0 0 6 21.75h12a2.25 2.25 0 0 0 2.25-2.25v-8.25A2.25 2.25 0 0 0 18 9h-1.5V6A4.5 4.5 0 0 0 12 1.5Zm3 7.5V6A3 3 0 0 0 9 6V9h6Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    v-model="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    autocomplete="current-password"
                                    class="w-full rounded-2xl border border-slate-200 bg-white/85 py-3 pl-12 pr-14 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Digite a sua senha">
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 transition hover:text-blue-600 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:text-slate-400 dark:hover:text-blue-300"
                                    :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                                    :aria-pressed="showPassword"
                                    @click="showPassword = !showPassword">
                                    <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                        <path d="M3 3l18 18"></path>
                                        <path d="M10.58 10.58a2 2 0 1 0 2.83 2.83"></path>
                                        <path d="M16.68 16.69A8.7 8.7 0 0 1 12 18c-5 0-9-6-9-6a18.7 18.7 0 0 1 5.02-4.62"></path>
                                        <path d="M9.88 5.09A9.12 9.12 0 0 1 12 5c5 0 9 6 9 6a18.85 18.85 0 0 1-1.95 2.79"></path>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                        <path d="M1 12s4-6 11-6 11 6 11 6-4 6-11 6S1 12 1 12z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="pendingTwoFactor">
                            <label for="two-factor-code" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Código de verificação</label>
                            <input
                                id="two-factor-code"
                                v-model="twoFactorCode"
                                type="text"
                                inputmode="numeric"
                                maxlength="6"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white/85 py-3 px-4 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                placeholder="Introduza o código de 6 dígitos">
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Verifique o código enviado para o seu email.</p>
                        </div>

                        <div v-if="!pendingTwoFactor" class="flex flex-col gap-3 text-sm text-slate-600 dark:text-slate-300 sm:flex-row sm:items-center sm:justify-between">
                            <label class="inline-flex items-center gap-3 font-medium">
                                <input v-model="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                Lembrar de mim
                            </label>
                        </div>

                        <p v-if="errorMessage" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">{{ errorMessage }}</p>

                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:translate-y-[-1px] hover:shadow-xl hover:shadow-blue-500/30 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-70">
                            {{ isLoading ? (pendingTwoFactor ? 'A validar...' : 'Entrando...') : (pendingTwoFactor ? 'Validar código' : 'Entrar') }}
                        </button>

                        <button
                            v-if="pendingTwoFactor"
                            type="button"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            @click="resetTwoFactorState">
                            Voltar
                        </button>
                    </form>
                </div>

                <div :class="[
                    'order-2 flex items-center bg-gradient-to-br from-blue-500 via-indigo-500 to-blue-700 px-6 py-8 text-white transition-all duration-500 ease-out lg:order-2 lg:px-14 lg:py-14',
                    isLeaving ? 'translate-x-10 opacity-0' : 'translate-x-0 opacity-100'
                ]">
                    <div class="flex w-full justify-center">
                        <div class="relative max-w-md rounded-[2rem] border border-white/20 bg-white/10 p-6 shadow-[inset_0_1px_0_rgba(255,255,255,0.18)] backdrop-blur-sm sm:p-8">
                            <button
                                type="button"
                                class="absolute right-3 top-3 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/50 bg-white/15 text-white shadow-lg backdrop-blur transition hover:scale-105 hover:bg-white/25 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-white/50 lg:-right-5 lg:-top-11"
                                aria-label="Fechar pagina"
                                title="Fechar"
                                @click="navigateWithTransition('/')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                                    <path d="M6 6l12 12"></path>
                                    <path d="M18 6l-12 12"></path>
                                </svg>
                            </button>

                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-100">To-Do App</p>
                            <h2 class="mt-4 text-2xl font-black tracking-tight sm:text-3xl lg:text-4xl">Organize tudo num so lugar</h2>
                            <p class="mt-3 text-sm leading-6 text-blue-50/90 hidden sm:block">
                                Crie a sua conta para centralizar tarefas, definir prazos e acompanhar o progresso diario sem confusao.
                            </p>

                            <div class="mt-4 sm:mt-8 grid grid-cols-2 gap-3 text-sm text-blue-50/90">
                                <div class="rounded-2xl bg-white/10 px-4 py-3">
                                    <span class="block text-2xl font-black text-white">Listas</span>
                                    <span>Organize tarefas por prioridade</span>
                                </div>
                                <div class="rounded-2xl bg-white/10 px-4 py-3">
                                    <span class="block text-2xl font-black text-white">Prazos</span>
                                    <span>Acompanhe entregas e progresso</span>
                                </div>
                            </div>

                            <a
                                href="/register"
                                @click.prevent="navigateWithTransition('/register')"
                                class="mt-8 inline-flex w-full items-center justify-center rounded-2xl border border-white/60 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-white/50 sm:w-auto">
                                Registrar
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <button
            id="theme-toggle"
            type="button"
            class="fixed bottom-4 right-4 z-50 rounded-full border border-white/70 bg-white/85 p-3 text-slate-700 shadow-lg backdrop-blur transition hover:scale-105 hover:bg-white focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-700 dark:bg-slate-900/85 dark:text-slate-100 dark:hover:bg-slate-900"
            :aria-label="themeLabel"
            :aria-pressed="isDark"
            title="Alternar tema"
            @click="toggleTheme">
            <span class="sr-only">{{ themeLabel }}</span>
            <svg v-show="!isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
            </svg>
            <svg v-show="isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                <path d="M21 12.79A9 9 0 1 1 11.21 3c0.25 0 0.5 0.01 0.75 0.04A7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const email = ref('');
const password = ref('');
const remember = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');
const isDark = ref(false);
const isLeaving = ref(false);
const showPassword = ref(false);
const pendingTwoFactor = ref(false);
const twoFactorCode = ref('');

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const themeLabel = computed(() => (isDark.value ? 'Ativar tema claro' : 'Ativar tema escuro'));

function syncThemeState() {
    isDark.value = document.documentElement.classList.contains('dark');
}

function handleStorage(event) {
    if (event.key === 'theme') {
        syncThemeState();
    }
}

function toggleTheme() {
    const dark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    isDark.value = dark;
}

function navigateWithTransition(url) {
    if (isLeaving.value) {
        return;
    }

    isLeaving.value = true;
    window.setTimeout(() => {
        window.location.href = url;
    }, 360);
}

async function submitLogin() {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value,
                remember: remember.value,
            }),
        });

        const data = await response.json().catch(() => ({}));

        if (response.ok && data?.requires_two_factor) {
            pendingTwoFactor.value = true;
            errorMessage.value = data?.message || 'Introduza o código enviado para o seu email.';
            return;
        }

        if (response.ok) {
            window.location.href = data?.redirect || '/todo';
            return;
        }

        errorMessage.value = data?.message || data?.errors?.email?.[0] || 'Nao foi possivel entrar.';
    } catch {
        errorMessage.value = 'Erro de conexao. Tente novamente.';
    } finally {
        isLoading.value = false;
    }
}

function resetTwoFactorState() {
    pendingTwoFactor.value = false;
    twoFactorCode.value = '';
    errorMessage.value = '';
}

async function submitTwoFactor() {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const response = await fetch('/login/verify-2fa', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify({
                code: twoFactorCode.value,
            }),
        });

        const data = await response.json().catch(() => ({}));

        if (response.ok) {
            window.location.href = data?.redirect || '/todo';
            return;
        }

        errorMessage.value = data?.message || 'Nao foi possivel validar o codigo.';
    } catch {
        errorMessage.value = 'Erro de conexao. Tente novamente.';
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    syncThemeState();
    window.addEventListener('storage', handleStorage);
});

onUnmounted(() => {
    window.removeEventListener('storage', handleStorage);
});
</script>
