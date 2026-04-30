<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>To-Do App | Aplicacao</title>
        <link rel="icon" type="image/png" href="{{ asset('image/logo/icon.png') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            (function () {
                const storedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>
    <body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-cyan-100 text-slate-900 transition-colors dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100">
        <button
            id="theme-toggle"
            type="button"
            class="fixed bottom-4 right-4 z-50 rounded-full border border-slate-300 bg-white p-3 shadow-md transition hover:scale-105 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800"
            aria-label="Alternar tema"
            title="Alternar tema"
        >
            <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
            </svg>
            <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden h-5 w-5">
                <path d="M21 12.79A9 9 0 1 1 11.21 3c0.25 0 0.5 0.01 0.75 0.04A7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>
        <div id="app" class="min-h-screen px-4 py-10"></div>

        <footer class="border-t border-slate-200 bg-white/70 px-6 py-8 text-center text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
            <p>&copy; {{ date('Y') }} Inovcorp. Todos os direitos reservados.</p>
        </footer>

        <script>
            (function () {
                const toggleButton = document.getElementById('theme-toggle');
                const sunIcon = document.getElementById('icon-sun');
                const moonIcon = document.getElementById('icon-moon');

                function refreshIcons() {
                    const isDark = document.documentElement.classList.contains('dark');
                    sunIcon.classList.toggle('hidden', isDark);
                    moonIcon.classList.toggle('hidden', !isDark);
                }

                toggleButton.addEventListener('click', function () {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    refreshIcons();
                });

                refreshIcons();
            })();
        </script>
    </body>
</html>
