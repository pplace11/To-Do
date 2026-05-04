<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

// Service provider principal da aplicacao.
// E carregado automaticamente pelo Laravel no arranque.
// Usado para registar bindings no contentor de servicos e
// para executar logica de inicializacao da aplicacao.
class AppServiceProvider extends ServiceProvider
{
    /**
     * Regista servicos no contentor de injecao de dependencias.
     * Usar este metodo para fazer bind de interfaces a implementacoes
     * ou registar singletons personalizados.
     */
    public function register(): void
    {
        //
    }

    /**
     * Executa logica apos todos os servicos terem sido registados.
     * Usado para configuracoes globais que dependem do ambiente ou
     * de outros servicos ja inicializados.
     */
    public function boot(): void
    {
        // Em producao, forca todas as URLs geradas a usar HTTPS.
        // Necessario quando a aplicacao esta atras de um proxy reverso (ex: Render, Heroku).
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
