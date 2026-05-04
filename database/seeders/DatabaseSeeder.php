<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Seeder principal da aplicacao. Executado com 'php artisan db:seed'.
// Responsavel por popular a base de dados com dados iniciais para desenvolvimento e testes.
class DatabaseSeeder extends Seeder
{
    // Desativa o disparo de eventos de modelo durante o seeding.
    // Evita efeitos secundarios (ex: envio de emails, listeners) ao inserir dados de teste.
    use WithoutModelEvents;

    /**
     * Popula a base de dados com dados iniciais.
     * Executado com 'php artisan db:seed' ou automaticamente apos 'php artisan migrate --seed'.
     */
    public function run(): void
    {
        // Exemplo comentado: criaria 10 utilizadores aleatorios via factory.
        // User::factory(10)->create();

        // Cria um utilizador de teste com nome e email fixos.
        // A password e gerada automaticamente pela UserFactory (ver database/factories/UserFactory.php).
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
