<?php

/**
 * Ficheiro principal de rotas web da aplicação.
 *
 * Define todas as rotas HTTP acessíveis pelo browser (com suporte a sessão e CSRF).
 * Está dividido em três grupos:
 *   1. Rota pública — landing page
 *   2. Rotas de convidado (guest) — login e registo (inacessíveis a utilizadores já autenticados)
 *   3. Rotas autenticadas (auth) — aplicação To-Do, perfil e logout
 *
 * No final, inclui o ficheiro tasks.php que regista as rotas da API de tarefas,
 * também protegidas pelo middleware 'auth'.
 */

// Controlador responsável pelo login, registo, verificação 2FA e logout
use App\Http\Controllers\AuthController;

// Controlador responsável pela consulta e atualização do perfil do utilizador
use App\Http\Controllers\ProfileController;

// Facade de registo de rotas do Laravel
use Illuminate\Support\Facades\Route;

/**
 * Rota pública — Landing page.
 *
 * GET / — devolve a view welcome.blade.php, que monta o componente Vue WelcomeApp.
 * Acessível por qualquer visitante, autenticado ou não.
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * Grupo de rotas protegido pelo middleware 'guest'.
 *
 * O middleware 'guest' impede que utilizadores já autenticados acedam a estas rotas.
 * Se um utilizador autenticado tentar aceder, é redirecionado para a rota home (por defeito '/').
 * Garante que o fluxo de login/registo só é apresentado a visitantes não autenticados.
 */
Route::middleware('guest')->group(function () {

    // GET /login — apresenta o formulário de login (monta LoginPage.vue via todo.blade.php)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    // POST /login — processa as credenciais e autentica o utilizador (ou inicia fluxo 2FA)
    Route::post('/login', [AuthController::class, 'login']);

    // POST /login/verify-2fa — valida o código de autenticação de dois fatores enviado por e-mail
    Route::post('/login/verify-2fa', [AuthController::class, 'verifyTwoFactor']);

    // GET /register — apresenta o formulário de registo (monta RegisterPage.vue)
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    // POST /register — cria o novo utilizador e autentica-o automaticamente
    Route::post('/register', [AuthController::class, 'register']);
});

/**
 * Grupo de rotas protegido pelo middleware 'auth'.
 *
 * O middleware 'auth' verifica a existência de uma sessão autenticada válida.
 * Utilizadores não autenticados são redirecionados para a rota 'login'.
 *
 * As rotas de visualização (/todo, /todo/nova, /perfil) devolvem todas a mesma
 * view todo.blade.php — o roteamento entre páginas é feito pelo lado do cliente
 * em app.js com base em window.location.pathname (SPA manual sem vue-router).
 */
Route::middleware('auth')->group(function () {

    // GET /todo — ecrã principal da aplicação To-Do (monta ToDoApp.vue)
    Route::get('/todo', function () {
        return view('todo');
    });

    // GET /todo/nova — ecrã de criação de nova tarefa (monta TaskCreatePage.vue via app.js)
    Route::get('/todo/nova', function () {
        return view('todo');
    });

    // GET /perfil — ecrã de perfil do utilizador (monta ProfilePage.vue via app.js)
    Route::get('/perfil', function () {
        return view('todo');
    });

    // GET /profile — devolve os dados do utilizador autenticado em JSON (consumido pelo Vue)
    Route::get('/profile', [ProfileController::class, 'show']);

    // PATCH /profile — atualiza campos do perfil (nome, e-mail, etc.)
    Route::patch('/profile', [ProfileController::class, 'update']);

    // PUT /profile/password — altera a palavra-passe do utilizador autenticado
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // PUT /profile/two-factor — ativa ou desativa a autenticação de dois fatores
    Route::put('/profile/two-factor', [ProfileController::class, 'updateTwoFactor']);

    // POST /logout — termina a sessão do utilizador e redireciona para a landing page
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Inclui as rotas da API de tarefas definidas em routes/tasks.php
// (também protegidas pelo middleware 'auth', conforme declarado nesse ficheiro)
require __DIR__.'/tasks.php';
