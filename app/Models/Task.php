<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modelo que representa uma tarefa na base de dados.
// Corresponde à tabela 'tasks' e pertence a um utilizador (user_id).
class Task extends Model
{
    // Permite a criacao de instancias via factory (usado em testes e seeders)
    use HasFactory;

    // Define os campos que podem ser preenchidos em massa (mass assignment).
    // Campos nao listados aqui serao ignorados em Task::create() e $task->update().
    protected $fillable = [
        'title',       // Titulo da tarefa (obrigatorio)
        'description', // Descricao detalhada da tarefa (opcional)
        'due_date',    // Data de entrega da tarefa (opcional)
        'priority',    // Prioridade: 'alta', 'media' ou 'baixa'
        'status',      // Estado: 'pendente' ou 'concluida'
        'user_id',     // ID do utilizador dono da tarefa
    ];
}
