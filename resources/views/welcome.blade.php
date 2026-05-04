{{-- View Blade da landing page (página de boas-vindas) --}}
{{-- Serve como casca HTML mínima para o componente Vue WelcomeApp --}}
{{-- Não inclui botão de tema nem footer próprios — ambos são geridos dentro do WelcomeApp.vue --}}
{{-- O script IIFE no <head> aplica o tema antes do render para evitar flash (FOUC) --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>To-Do App</title>
        {{-- Favicon da aplicação --}}
        <link rel="icon" type="image/png" href="{{ asset('image/logo/icon.png') }}">
        {{-- Vite: injeta os assets CSS e JS da landing page (welcome.js monta o WelcomeApp) --}}
        @vite(['resources/css/app.css', 'resources/js/welcome.js'])
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
    <body>
        {{-- Ponto de montagem do Vue — o componente WelcomeApp é injetado aqui pelo welcome.js --}}
        {{-- Todo o layout visual (header, hero, features, botão de tema, footer) está dentro do WelcomeApp.vue --}}
        <div id="app"></div>
    </body>
</html>
