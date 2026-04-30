<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private function currentUserId(): int
    {
        return (int) Auth::id();
    }

    public function index(Request $request)
    {
        $query = Task::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->has('due_date')) {
            $query->whereDate('due_date', '=', $request->due_date, 'and');
        }
        $tasks = $query->where('user_id', '=', $this->currentUserId())->orderBy('due_date', 'asc')->get();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:alta,media,baixa',
        ]);
        $validated['user_id'] = $this->currentUserId();
        $validated['status'] = 'pendente';
        $task = Task::create($validated);
        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'sometimes|required|in:alta,media,baixa',
            'status' => 'sometimes|required|in:pendente,concluida',
        ]);
        $task->update($validated);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        Task::destroy($task->id);
        return response()->json(null, 204);
    }

    public function share(Request $request, Task $task)
    {
        if ($task->user_id !== $this->currentUserId()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $recipient = User::query()->where('email', $validated['email'])->first();

        if (!$recipient) {
            return response()->json(['message' => 'Utilizador não encontrado com este e-mail.'], 404);
        }

        if ((int) $recipient->id === $this->currentUserId()) {
            return response()->json(['message' => 'Não pode partilhar uma tarefa consigo mesmo.'], 422);
        }

        $alreadyExists = Task::query()
            ->where('user_id', $recipient->id)
            ->where('title', $task->title)
            ->where('description', $task->description)
            ->whereDate('due_date', '=', $task->due_date, 'and')
            ->exists();

        if ($alreadyExists) {
            return response()->json(['message' => 'Esta tarefa já foi partilhada com este utilizador.'], 409);
        }

        $sharedTask = Task::create([
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date,
            'priority' => $task->priority,
            'status' => 'pendente',
            'user_id' => $recipient->id,
        ]);

        return response()->json([
            'message' => 'Tarefa partilhada com sucesso.',
            'task' => $sharedTask,
        ], 201);
    }
}
