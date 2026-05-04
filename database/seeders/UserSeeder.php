<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// Seeder especifico para criar utilizadores de desenvolvimento.
// Pode ser executado individualmente com 'php artisan db:seed --class=UserSeeder'.
class UserSeeder extends Seeder
{
    // Popula a base de dados com um utilizador de desenvolvimento.
    // Usa firstOrCreate para evitar duplicados caso o seeder seja executado mais de uma vez.
    public function run(): void
    {
        // Cria o utilizador apenas se ainda nao existir um registo com este email.
        // O primeiro argumento e a condicao de pesquisa; o segundo sao os dados a preencher na criacao.
        User::firstOrCreate(
            ['email' => 'dev@todo.test'],   // Condicao: procura pelo email
            [
                'name'     => 'Dev User',           // Nome do utilizador de desenvolvimento
                'password' => bcrypt('password'),   // Password encriptada com bcrypt
            ]
        );
    }
}
