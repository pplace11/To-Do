<?php

/**
 * Ficheiro de testes de funcionalidade (Feature Tests) para o recurso Task.
 *
 * Cobre os principais cenários CRUD das tarefas, incluindo:
 *   - Listagem de tarefas próprias e isolamento de tarefas alheias
 *   - Criação com dados válidos e rejeição com dados inválidos
 *   - Atualização e eliminação de tarefas do utilizador autenticado
 *
 * Utiliza o Pest PHP (sintaxe funcional com `test()`).
 * Cada teste usa factories para criar dados de teste isolados em base de dados de teste.
 * actingAs($user) autentica o utilizador para o pedido sem necessidade de sessão real.
 */

// Modelo Task — usado para criar tarefas de teste via factory
use App\Models\Task;

// Modelo User — usado para criar utilizadores de teste via factory
use App\Models\User;

/**
 * Teste: o utilizador consegue listar as suas próprias tarefas.
 *
 * Cria um utilizador e uma tarefa associada a esse utilizador.
 * Autentica o utilizador e envia GET /tasks.
 * Verifica que a resposta é 200 OK e que o título da tarefa está presente no JSON.
 */
test('user can fetch their tasks', function () {
    // Cria um utilizador de teste na base de dados
    $user = User::factory()->create();

    // Cria uma tarefa associada ao utilizador criado
    $task = Task::factory()->create(['user_id' => $user->id]);

    // Envia pedido GET /tasks autenticado como $user e espera resposta JSON
    $response = $this->actingAs($user)->getJson('/tasks');

    // Verifica HTTP 200 OK e que o título da tarefa está no corpo da resposta
    $response->assertOk()
        ->assertJsonFragment(['title' => $task->title]);
});

/**
 * Teste: o utilizador não consegue ver as tarefas de outros utilizadores.
 *
 * Cria dois utilizadores distintos; a tarefa pertence ao segundo ($user2).
 * Autentica o primeiro ($user1) e verifica que a tarefa de $user2
 * NÃO aparece na listagem devolvida — confirma o isolamento de dados entre utilizadores.
 */
test('user cannot fetch other users tasks', function () {
    // Cria dois utilizadores independentes
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Cria uma tarefa pertencente apenas ao segundo utilizador
    $task = Task::factory()->create(['user_id' => $user2->id]);

    // Autentica como $user1 e lista as suas tarefas
    $response = $this->actingAs($user1)->getJson('/tasks');

    // Verifica HTTP 200 OK — o pedido é válido, mas...
    $response->assertOk();

    // ...o título da tarefa de $user2 não deve aparecer na resposta de $user1
    $response->assertJsonMissing(['title' => $task->title]);
});

/**
 * Teste: o utilizador consegue criar uma nova tarefa com dados válidos.
 *
 * Envia POST /tasks com todos os campos obrigatórios e opcionais.
 * Verifica:
 *   - Resposta HTTP 201 Created com o título no JSON
 *   - Registo gravado corretamente na base de dados com o user_id correto
 */
test('user can create a task', function () {
    // Cria um utilizador de teste
    $user = User::factory()->create();

    // Envia pedido POST /tasks com dados válidos da nova tarefa
    $response = $this->actingAs($user)->postJson('/tasks', [
        'title'       => 'Nova Tarefa',       // Campo obrigatório
        'description' => 'Descrição teste',   // Campo opcional
        'priority'    => 'alta',              // Enum: baixa | media | alta
        'due_date'    => '2026-05-01'         // Data limite no formato Y-m-d
    ]);

    // Verifica HTTP 201 Created e que o título está presente no JSON de resposta
    $response->assertCreated()
        ->assertJsonFragment(['title' => 'Nova Tarefa']);

    // Confirma que o registo foi efetivamente gravado na base de dados com o user_id correto
    $this->assertDatabaseHas('tasks', ['title' => 'Nova Tarefa', 'user_id' => $user->id]);
});

/**
 * Teste: o utilizador consegue atualizar uma tarefa que lhe pertence.
 *
 * Envia PUT /tasks/{id} com novos valores para título e estado.
 * Verifica:
 *   - Resposta HTTP 200 OK
 *   - Base de dados atualizada com os novos valores
 */
test('user can update their task', function () {
    // Cria utilizador e uma tarefa associada
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    // Envia pedido PUT com os campos a atualizar
    $response = $this->actingAs($user)->putJson("/tasks/{$task->id}", [
        'title'  => 'Tarefa Atualizada', // Novo título
        'status' => 'concluida'          // Novo estado (enum: pendente | em_progresso | concluida)
    ]);

    // Verifica HTTP 200 OK
    $response->assertOk();

    // Confirma que a base de dados reflete as alterações enviadas
    $this->assertDatabaseHas('tasks', [
        'id'     => $task->id,
        'title'  => 'Tarefa Atualizada',
        'status' => 'concluida'
    ]);
});

/**
 * Teste: o utilizador consegue eliminar uma tarefa que lhe pertence.
 *
 * Envia DELETE /tasks/{id} e verifica:
 *   - Resposta HTTP 204 No Content (sem corpo na resposta)
 *   - Registo removido da base de dados
 */
test('user can delete their task', function () {
    // Cria utilizador e uma tarefa associada
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    // Envia pedido DELETE para eliminar a tarefa
    $response = $this->actingAs($user)->deleteJson("/tasks/{$task->id}");

    // Verifica HTTP 204 No Content — eliminação bem-sucedida sem corpo de resposta
    $response->assertNoContent();

    // Confirma que o registo já não existe na base de dados
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

/**
 * Teste: a criação de uma tarefa falha quando os dados são inválidos.
 *
 * Envia POST /tasks sem o campo obrigatório 'title'.
 * Verifica que a resposta é HTTP 422 Unprocessable Entity,
 * confirmando que a validação do Laravel rejeita pedidos incompletos.
 */
test('task creation requires valid data', function () {
    // Cria um utilizador de teste
    $user = User::factory()->create();

    // Envia pedido POST sem o campo 'title' (obrigatório)
    $response = $this->actingAs($user)->postJson('/tasks', [
        'description' => 'Descrição sem título' // Apenas o campo opcional — título em falta
    ]);

    // Verifica HTTP 422 Unprocessable Entity — validação falhou
    $response->assertUnprocessable();
});
