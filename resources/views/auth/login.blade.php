<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Entrar | To-Do App</title>
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
    <body>
        <div id="app"></div>
    </body>
</html>
