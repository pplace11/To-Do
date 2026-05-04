<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controller responsavel pela gestao das tarefas (CRUD) do utilizador autenticado.
// Inclui listagem com filtros, criacao, visualizacao, atualizacao,
// remocao e partilha de tarefas com outros utilizadores.
class TaskController extends Controller
{
    // Metodo auxiliar que retorna o ID do utilizador autenticado como inteiro.
    // Usado em todos os metodos para verificar a propriedade das tarefas.
    private function currentUserId(): int
    {
        return (int) Auth::id();
    }

    // Lista todas as tarefas do utilizador autenticado, ordenadas por data de entrega.
    // Suporta filtros opcionais por: status, priority e due_date (via query string).
    public function index(Request $request)
    {
        $query = Task::query();

        // Aplica filtro por status se presente na requisicao
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Aplica filtro por prioridade se presente na requisicao
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Aplica filtro por data de entrega se presente na requisicao
        if ($request->has('due_date')) {
            $query->whereDate('due_date', '=', $request->due_date, 'and');
        }

        // Restringe os resultados ao utilizador autenticado e ordena por due_date ascendente
        $tasks = $query->where('user_id', '=', $this->currentUserId())->orderBy('due_date', 'asc')->get();
        return response()->json($tasks);
    }

    // Cria uma nova tarefa para o utilizador autenticado.
    // O status e definido automaticamente como 'pendente' na criacao.
    public function store(Request $request)
    {
        // Valida os campos obrigatorios e opcionais da tarefa
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:alta,media,baixa',
        ]);

        // Associa a tarefa ao utilizador autenticado e define o status inicial
        $validated['user_id'] = $this->currentUserId();
        $validated['status'] = 'pendente';

        // Cria a tarefa na base de dados e retorna com codigo 201 (Created)
        $task = Task::create($validated);
        return response()->json($task, 201);
    }

    // Retorna os detalhes de uma tarefa especifica.
    // Apenas o dono da tarefa pode aceder a este recurso.
    public function show(Task $task)
    {
        // Verifica se a tarefa pertence ao utilizador autenticado
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($task);
    }

    // Atualiza os dados de uma tarefa existente.
    // Apenas o dono da tarefa pode efetuar alteracoes.
    // Todos os campos sao opcionais (partial update com 'sometimes').
    public function update(Request $request, Task $task)
    {
        // Verifica se a tarefa pertence ao utilizador autenticado
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Valida apenas os campos presentes na requisicao
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|required|in:alta,media,baixa',
            'status' => 'sometimes|required|in:pendente,concluida',
        ]);

        // Atualiza a tarefa e retorna os dados atualizados
        $task->update($validated);
        return response()->json($task);
    }

    // Remove permanentemente uma tarefa da base de dados.
    // Apenas o dono da tarefa pode elimina-la.
    public function destroy(Task $task)
    {
        // Verifica se a tarefa pertence ao utilizador autenticado
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Elimina a tarefa e retorna resposta vazia com codigo 204 (No Content)
        Task::destroy($task->id);
        return response()->json(null, 204);
    }

    // Partilha uma tarefa com outro utilizador identificado pelo email.
    // Cria uma copia da tarefa na conta do destinatario com status 'pendente'.
    // Nao e possivel partilhar consigo mesmo nem duplicar partilhas ja existentes.
    public function share(Request $request, Task $task)
    {
        // Verifica se a tarefa pertence ao utilizador autenticado
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Valida o email do destinatario
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Tenta encontrar o utilizador destinatario pelo email
        $recipient = User::query()->where('email', $validated['email'])->first();

        // Retorna erro 404 se o utilizador nao existir
        if (!$recipient) {
            return response()->json(['message' => 'Utilizador não encontrado com este e-mail.'], 404);
        }

        // Impede a partilha consigo mesmo
        if ((int) $recipient->id === $this->currentUserId()) {
            return response()->json(['message' => 'Não pode partilhar uma tarefa consigo mesmo.'], 422);
        }

        // Verifica se ja existe uma tarefa identica na conta do destinatario (evita duplicados)
        $alreadyExists = Task::query()
            ->where('user_id', $recipient->id)
            ->where('title', $task->title)
            ->where('description', $task->description)
            ->whereDate('due_date', '=', $task->due_date, 'and')
            ->exists();

        // Retorna erro 409 se a tarefa ja foi partilhada anteriormente
        if ($alreadyExists) {
            return response()->json(['message' => 'Esta tarefa já foi partilhada com este utilizador.'], 409);
        }

        // Cria uma copia da tarefa para o destinatario com status 'pendente'
        $sharedTask = Task::create([
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date,
            'priority' => $task->priority,
            'status' => 'pendente',
            'user_id' => $recipient->id,
        ]);

        // Retorna a tarefa criada com codigo 201 (Created)
        return response()->json([
            'message' => 'Tarefa partilhada com sucesso.',
            'task' => $sharedTask,
        ], 201);
    }
}
