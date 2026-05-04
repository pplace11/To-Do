<?php

/**
 * Ficheiro de rotas de consola (Artisan commands via closures).
 *
 * Este ficheiro é carregado automaticamente pelo Laravel durante o arranque
 * e permite registar comandos Artisan diretamente como closures,
 * sem necessidade de criar uma classe Command dedicada.
 *
 * Usado para comandos simples ou utilitários de desenvolvimento.
 */

// Classe utilitária do Laravel que devolve citações inspiradoras aleatórias
use Illuminate\Foundation\Inspiring;

// Facade que dá acesso ao motor de comandos Artisan (registo e execução)
use Illuminate\Support\Facades\Artisan;

/**
 * Comando Artisan: php artisan inspire
 *
 * Exibe uma citação inspiradora aleatória no terminal.
 * Serve como exemplo base de como registar comandos via closure neste ficheiro.
 *
 * Dentro da closure, $this refere-se ao objeto Command do Laravel,
 * o que dá acesso a métodos de output como comment(), info(), error(), etc.
 *
 * ->purpose(): define a descrição do comando visível em `php artisan list`
 */
Artisan::command('inspire', function () {
    // Obtém uma citação aleatória e exibe-a formatada (texto cinzento/amarelo no terminal)
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
