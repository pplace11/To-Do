/**
 * vite.config.js — Configuração do Vite para a aplicação To-Do (Laravel + Vue + Tailwind)
 *
 * O Vite é o bundler de assets utilizado em desenvolvimento e produção.
 * Em desenvolvimento: servidor HMR (Hot Module Replacement) com atualização instantânea.
 * Em produção (`npm run build`): gera assets minificados com hash de versão em public/build/.
 *
 * Plugins ativos:
 *   - laravel-vite-plugin : integração com o Laravel (@vite Blade directive, manifesto)
 *   - @tailwindcss/vite   : processa as classes Tailwind CSS v4 diretamente no Vite
 *   - @vitejs/plugin-vue  : suporte a componentes Vue 3 Single File Components (.vue)
 */

// Função utilitária do Vite para definir a configuração com suporte a IntelliSense
import { defineConfig } from 'vite';

// Plugin oficial de integração Laravel — gera o manifesto e resolve os paths dos assets
import laravel from 'laravel-vite-plugin';

// Plugin Tailwind CSS v4 para Vite — processa @tailwind directives e classes utilitárias
import tailwindcss from '@tailwindcss/vite';

// Plugin Vue 3 — permite importar e compilar ficheiros .vue (template + script + style)
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        // Plugin Laravel: define os pontos de entrada (entry points) da aplicação
        laravel({
            input: [
                'resources/css/app.css',      // CSS principal (Tailwind + estilos globais)
                'resources/js/app.js',        // Entry point principal — monta o Vue para /todo e outras páginas
                'resources/js/welcome.js',    // Entry point separado — monta o WelcomeApp para a landing page
            ],
            // refresh: true — recarrega automaticamente a página quando ficheiros Blade ou PHP de rotas
            // são alterados durante o desenvolvimento (equivalente a live reload para o backend)
            refresh: true,
        }),

        // Plugin Tailwind CSS v4 — integração nativa com Vite (substitui o postcss plugin)
        tailwindcss(),

        // Plugin Vue 3 — habilita o suporte a Single File Components (.vue)
        vue(),
    ],

    // Configuração do servidor de desenvolvimento (apenas relevante com `npm run dev`)
    server: {
        watch: {
            // Exclui a pasta de views compiladas pelo Blade do sistema de watch do Vite.
            // Sem esta exclusão, o Vite tentaria observar os ficheiros PHP gerados pelo
            // `view:cache` do Laravel, causando ciclos de reload desnecessários.
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
