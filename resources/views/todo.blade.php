{{-- View Blade principal da aplicação To-Do --}}
{{-- Serve como casca HTML para o componente Vue ToDoApp --}}
{{-- Responsável por: tema inicial sem flash, botão de alternância de tema (JS puro), --}}
{{-- injeção do token CSRF na meta tag, montagem do #app pelo Vue e footer global --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- Token CSRF injetado na meta tag — lido pelo JavaScript (app.js) para incluir nos cabeçalhos das mutações --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>To-Do App | Aplicação</title>
        {{-- Favicon da aplicação --}}
        <link rel="icon" type="image/png" href="{{ asset('image/logo/icon.png') }}">
        {{-- Vite: injeta os assets CSS e JS compilados (com hash de versão em produção) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Script de inicialização do tema — executado imediatamente (IIFE) antes do render
            // Evita o "flash of unstyled content" (FOUC) ao aplicar a classe 'dark' antes de o browser pintar a página
            (function () {
                const storedTheme = localStorage.getItem('theme'); // Preferência guardada pelo utilizador
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; // Preferência do SO
                // Aplica dark mode se: o utilizador já escolheu dark, ou se não há preferência guardada e o SO prefere dark
                if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>
    {{-- Body: gradiente claro em light mode, gradiente radial escuro em dark mode --}}
    <body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-cyan-100 text-slate-900 transition-colors dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100">

        {{-- Botão de alternância de tema (JS puro, fora do Vue) --}}
        {{-- Posicionado fixo no canto inferior direito; z-50 garante que fica acima do conteúdo --}}
        {{-- Os ícones sol/lua são alternados via refreshIcons() no script abaixo --}}
        <button
            id="theme-toggle"
            type="button"
            class="fixed bottom-4 right-4 z-50 rounded-full border border-slate-300 bg-white p-3 shadow-md transition hover:scale-105 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800"
            aria-label="Alternar tema"
            title="Alternar tema"
        >
            {{-- Ícone de sol — visível em light mode; oculto em dark mode via classe 'hidden' --}}
            <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
            </svg>
            {{-- Ícone de lua — visível em dark mode; começa oculto (class="hidden") --}}
            <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden h-5 w-5">
                <path d="M21 12.79A9 9 0 1 1 11.21 3c0.25 0 0.5 0.01 0.75 0.04A7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>

        {{-- Ponto de montagem do Vue — o componente ToDoApp é injetado aqui pelo app.js --}}
        {{-- min-h-screen garante que o fundo preenche o ecrã mesmo com pouco conteúdo --}}
        <div id="app" class="min-h-screen px-4 py-10"></div>

        {{-- Footer global da página (fora do Vue para garantir visibilidade mesmo antes do bundle carregar) --}}
        {{-- date('Y'): ano atual gerado pelo PHP no momento do render do Blade --}}
        <footer class="border-t border-slate-200 bg-white/70 px-6 py-8 text-center text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
            <p>&copy; {{ date('Y') }} Inovcorp. Todos os direitos reservados.</p>
        </footer>

        <script>
            // Script de controlo do botão de tema (JS puro, sem depender do Vue)
            // Necessário porque o botão está fora do #app e precisa funcionar imediatamente
            (function () {
                const toggleButton = document.getElementById('theme-toggle');
                const sunIcon = document.getElementById('icon-sun');
                const moonIcon = document.getElementById('icon-moon');

                // Atualiza a visibilidade dos ícones sol/lua conforme o tema atual
                // Chamada no arranque e após cada clique no botão
                function refreshIcons() {
                    const isDark = document.documentElement.classList.contains('dark');
                    sunIcon.classList.toggle('hidden', isDark);   // Oculta o sol em dark mode
                    moonIcon.classList.toggle('hidden', !isDark); // Oculta a lua em light mode
                }

                // Ao clicar: alterna a classe 'dark' no <html>, persiste no localStorage e atualiza os ícones
                toggleButton.addEventListener('click', function () {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    refreshIcons();
                });

                // Inicialização: sincroniza os ícones com o tema já aplicado pelo script do <head>
                refreshIcons();
            })();
        </script>
    </body>
</html>
