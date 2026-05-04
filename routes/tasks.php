<?php

/**
 * Ficheiro de rotas da API de tarefas.
 *
 * Define todos os endpoints REST relativos ao recurso Task.
 * Este ficheiro é incluído pelo web.php (ou registado no bootstrap)
 * e todas as rotas aqui definidas ficam protegidas pelo middleware 'auth',
 * garantindo que apenas utilizadores autenticados podem aceder às tarefas.
 *
 * Convenção de nomes dos métodos no TaskController:
 *   index   → listar todas as tarefas do utilizador autenticado
 *   store   → criar uma nova tarefa
 *   show    → devolver uma tarefa específica
 *   share   → partilhar uma tarefa com outro utilizador
 *   update  → atualizar os dados de uma tarefa existente
 *   destroy → eliminar uma tarefa
 */

// Classe Request do Laravel (disponível para uso em closures de rota, se necessário)
use Illuminate\Http\Request;

// Facade de registo de rotas
use Illuminate\Support\Facades\Route;

// Controlador responsável por toda a lógica CRUD das tarefas
use App\Http\Controllers\TaskController;

/**
 * Grupo de rotas protegido pelo middleware 'auth'.
 *
 * O middleware 'auth' verifica se existe uma sessão autenticada válida.
 * Caso o utilizador não esteja autenticado, é redirecionado para o login
 * (ou recebe 401, conforme configuração do guard em config/auth.php).
 *
 * O parâmetro {task} usa model binding implícito do Laravel:
 * o Laravel resolve automaticamente o modelo Task pelo id fornecido na URL,
 * lançando 404 se não existir, e aplica as políticas definidas em TaskPolicy.
 */
Route::middleware('auth')->group(function () {

    // GET /tasks — devolve todas as tarefas do utilizador autenticado (lista completa)
    Route::get('/tasks', [TaskController::class, 'index']);

    // POST /tasks — cria uma nova tarefa com os dados enviados no corpo do pedido
    Route::post('/tasks', [TaskController::class, 'store']);

    // GET /tasks/{task} — devolve os detalhes de uma tarefa específica pelo seu id
    Route::get('/tasks/{task}', [TaskController::class, 'show']);

    // POST /tasks/{task}/share — partilha a tarefa com outro utilizador (via e-mail ou id)
    Route::post('/tasks/{task}/share', [TaskController::class, 'share']);

    // PUT /tasks/{task} — atualiza os campos de uma tarefa existente (substituição parcial ou total)
    Route::put('/tasks/{task}', [TaskController::class, 'update']);

    // DELETE /tasks/{task} — elimina permanentemente a tarefa da base de dados
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
});
