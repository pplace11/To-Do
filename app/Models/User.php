<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// Descomentado caso seja necessario verificar o email apos registo.

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Define os campos que podem ser preenchidos em massa (mass assignment).
// Apenas 'name', 'email' e 'password' sao permitidos.
#[Fillable(['name', 'email', 'password'])]

// Oculta os campos sensiveis nas respostas JSON e arrays.
// 'password' e 'remember_token' nao serao expostos ao exterior.
#[Hidden(['password', 'remember_token'])]

// Modelo que representa o utilizador autenticado na aplicacao.
// Estende Authenticatable para suportar login, guards e autenticacao do Laravel.
// Inclui suporte a 2FA atraves dos campos two_factor_enabled, two_factor_code e two_factor_expires_at.
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // HasFactory: permite criar instancias via factory (testes/seeders).
    // Notifiable: permite enviar notificacoes ao utilizador (ex: email, SMS).
    use HasFactory, Notifiable;

    /**
     * Define os casts dos atributos do modelo.
     * Converte automaticamente os valores ao ler/escrever na base de dados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // Converte para instancia Carbon
            'password'          => 'hashed',     // Faz hash automaticamente ao guardar
            'two_factor_enabled'    => 'boolean',    // Converte para booleano
            'two_factor_expires_at' => 'datetime',   // Converte para instancia Carbon
        ];
    }
}
