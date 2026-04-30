<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top,_#dbeafe,_#eff6ff_35%,_#f8fafc_65%)] text-slate-900 transition-colors dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100">
    <a
      href="#main-content"
      class="sr-only z-[100] rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:outline-none focus:ring-4 focus:ring-blue-300"
    >
      Pular para o conteudo principal
    </a>

    <header class="sticky top-0 z-40 border-b border-blue-100/70 bg-white/85 backdrop-blur dark:border-slate-700/80 dark:bg-slate-950/85">
      <nav class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4" aria-label="Navegacao principal">
        <a
          href="/"
          class="inline-flex rounded-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300"
          aria-label="Voltar para a pagina inicial"
        >
          <img :src="'/image/logo/ToDo.png'" alt="Logotipo To-Do App" class="h-16 w-auto" />
        </a>

        <div class="flex items-center gap-3">
          <a
            href="/login"
            class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
          >
            Entrar
          </a>
          <a
            href="/register"
            class="inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300"
          >
            Registrar
          </a>
        </div>
      </nav>
    </header>

    <main id="main-content" tabindex="-1">
      <section class="px-6 pb-14 pt-16 sm:pt-20">
        <div class="mx-auto max-w-5xl text-center">
          <h1 class="mt-2 text-balance text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl md:text-6xl dark:text-white">
            Organize suas tarefas com clareza e velocidade
          </h1>

          <p class="mx-auto mt-6 max-w-3xl text-lg leading-relaxed text-slate-700 md:text-xl dark:text-slate-300">
            Transforme sua rotina com um To-Do rapido e inteligente: organize tarefas, defina prioridades e acompanhe cada status em tempo real.
          </p>

          <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
            <a
              href="/todo"
              class="inline-flex rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300"
            >
              Começar agora
            </a>
          </div>
        </div>
      </section>

      <section id="features" aria-labelledby="features-title" class="px-6 pb-16 pt-6">
        <div class="mx-auto max-w-6xl rounded-3xl border border-blue-100/80 bg-white/75 p-8 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/50 md:p-10">
          <h2 id="features-title" class="text-center text-3xl font-bold tracking-tight text-slate-900 md:text-4xl dark:text-slate-100">
            Funcionalidades principais
          </h2>

          <ul class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3" role="list">
            <li v-for="feature in features" :key="feature.title">
              <article class="h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-800/80">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ feature.title }}</h3>
                <p class="mt-2 text-slate-700 dark:text-slate-300">{{ feature.description }}</p>
              </article>
            </li>
          </ul>
        </div>
      </section>
    </main>

    <button
      id="theme-toggle"
      type="button"
      class="fixed bottom-4 right-4 z-50 rounded-full border border-slate-300 bg-white p-3 shadow-md transition hover:scale-105 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800"
      :aria-label="themeLabel"
      :aria-pressed="isDark"
      title="Alternar tema"
      @click="toggleTheme"
    >
      <span class="sr-only">{{ themeLabel }}</span>
      <svg v-show="!isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
        <circle cx="12" cy="12" r="4"></circle>
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
      </svg>
      <svg v-show="isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
        <path d="M21 12.79A9 9 0 1 1 11.21 3c0.25 0 0.5 0.01 0.75 0.04A7 7 0 0 0 21 12.79z"></path>
      </svg>
    </button>

    <footer class="border-t border-slate-200/80 bg-white/70 px-6 py-8 text-center text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
      <p>&copy; {{ year }} Inovcorp. Todos os direitos reservados.</p>
    </footer>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const isDark = ref(false);
const year = new Date().getFullYear();

const features = [
  {
    title: 'Registro de tarefas',
    description: 'Crie tarefas com titulo, descricao, data de vencimento e prioridade.',
  },
  {
    title: 'Filtros por status e prioridade',
    description: 'Visualize tarefas pendentes ou concluidas e refine por nivel de prioridade.',
  },
  {
    title: 'Ordenacao por vencimento',
    description: 'Organize a lista por datas mais proximas ou mais distantes.',
  },
  {
    title: 'Edicao de tarefas',
    description: 'Atualize titulo, descricao, vencimento e prioridade em um modal dedicado.',
  },
  {
    title: 'Conclusao de tarefas',
    description: 'Marque tarefas pendentes como concluidas com um clique.',
  },
  {
    title: 'Exclusao com confirmacao',
    description: 'Remova tarefas com etapa de confirmacao e feedback visual de sucesso ou erro.',
  },
];

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

onMounted(() => {
  syncThemeState();
  window.addEventListener('storage', handleStorage);
});

onUnmounted(() => {
  window.removeEventListener('storage', handleStorage);
});
</script>
