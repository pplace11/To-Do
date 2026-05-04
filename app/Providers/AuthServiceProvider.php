<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Task;
use App\Policies\TaskPolicy;

// Service provider responsavel pelo registo de politicas de autorizacao (Policies)
// e gates da aplicacao. E carregado automaticamente pelo Laravel no arranque.
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento entre modelos e as suas politicas de autorizacao.
     * O Laravel usa esta tabela para resolver automaticamente qual Policy
     * aplicar quando se usa Gate::authorize() ou $this->authorize() nos controllers.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // O modelo Task e gerido pela TaskPolicy (define quem pode criar, ver, editar, apagar)
        Task::class => TaskPolicy::class,
    ];

    /**
     * Regista as politicas de autorizacao e gates da aplicacao.
     * O metodo registerPolicies() carrega automaticamente os mapeamentos
     * definidos em $policies acima.
     */
    public function boot(): void
    {
        // Regista todas as policies definidas no array $policies
        $this->registerPolicies();
    }
}
