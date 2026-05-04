<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration responsavel por adicionar as colunas de autenticacao de dois fatores (2FA)
// à tabela 'users'. Estas colunas suportam o envio e validacao de codigos temporarios por email.
return new class extends Migration
{
    // Adiciona as tres colunas de 2FA à tabela 'users'.
    // As colunas sao inseridas a seguir ao campo 'password'.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false)->after('password');         // Indica se o 2FA esta ativo para o utilizador (falso por defeito)
            $table->string('two_factor_code')->nullable()->after('two_factor_enabled');       // Codigo temporario de 6 digitos enviado por email (nulo quando nao ha codigo pendente)
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code'); // Data e hora de expiracao do codigo 2FA (nulo quando nao ha codigo pendente)
        });
    }

    // Reverte a migration: remove as tres colunas de 2FA da tabela 'users'.
    // Executado com 'php artisan migrate:rollback'.
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove as colunas de 2FA em bloco
            $table->dropColumn([
                'two_factor_enabled',
                'two_factor_code',
                'two_factor_expires_at',
            ]);
        });
    }
};
