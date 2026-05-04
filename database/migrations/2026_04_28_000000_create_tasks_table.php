<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration responsavel por criar a tabela 'tasks' na base de dados.
// Define a estrutura de cada tarefa: titulo, descricao, data de entrega,
// prioridade, estado e associacao ao utilizador dono da tarefa.
return new class extends Migration
{
    // Executa a migration: cria a tabela 'tasks' com todas as suas colunas.
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();                                                          // Chave primaria auto-incrementavel
            $table->foreignId('user_id')->constrained()->onDelete('cascade');     // Chave estrangeira para 'users'; apaga as tarefas ao apagar o utilizador
            $table->string('title');                                               // Titulo da tarefa (obrigatorio)
            $table->text('description')->nullable();                               // Descricao detalhada (opcional)
            $table->date('due_date')->nullable();                                  // Data de entrega (opcional)
            $table->enum('priority', ['alta', 'media', 'baixa'])->default('media'); // Prioridade com valor padrao 'media'
            $table->enum('status', ['pendente', 'concluida'])->default('pendente'); // Estado com valor padrao 'pendente'
            $table->timestamps();                                                  // Colunas created_at e updated_at geridas pelo Laravel
        });
    }

    // Reverte a migration: remove a tabela 'tasks' caso exista.
    // Executado com 'php artisan migrate:rollback'.
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
