<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    // Retorna a view de login
    public function showLogin(): View
    {
        return view('auth.login');
    }

    // Retorna a view de registo
    public function showRegister(): View
    {
        return view('auth.register');
    }

    // Processa o login do utilizador
    // Valida as credenciais (email e password) e, se corretas, autentica o utilizador.
    // Se o utilizador tiver 2FA ativo, gera um codigo de 6 digitos, guarda-o na base de dados,
    // envia-o por email e coloca o ID do utilizador na sessao para verificacao posterior.
    // Caso contrario, autentica diretamente e redireciona para /todo.
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        // Valida os campos email e password da requisicao
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Tenta encontrar o utilizador pelo email
        $user = User::query()->where('email', $credentials['email'])->first();

        // Verifica se o utilizador existe e se a password esta correta
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Credenciais invalidas.',
                    'errors' => [
                        'email' => ['Credenciais invalidas.'],
                    ],
                ], 422);
            }

            // Retorna com erro de credenciais invalidas para respostas nao-JSON
            return back()->withErrors([
                'email' => 'Credenciais invalidas.',
            ])->onlyInput('email');
        }

        // Verifica se o utilizador tem autenticacao de dois fatores ativa
        if ($user->two_factor_enabled) {
            // Gera um codigo aleatorio de 6 digitos
            $code = (string) random_int(100000, 999999);

            // Guarda o codigo e a data de expiracao (10 minutos) no utilizador
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            // Envia o codigo por email ao utilizador
            Mail::raw("O seu codigo de autenticacao de dois fatores e: {$code}. Este codigo expira em 10 minutos.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Codigo de autenticacao - To-Do App');
            });

            // Guarda o ID do utilizador e a opcao "remember" na sessao para uso posterior
            $request->session()->put('auth.pending_2fa_user_id', $user->id);
            $request->session()->put('auth.pending_2fa_remember', $request->boolean('remember'));

            // Informa o cliente que e necessario verificar o codigo 2FA
            return response()->json([
                'message' => 'Enviamos um codigo de verificacao para o seu email.',
                'requires_two_factor' => true,
            ]);
        }

        // Autentica o utilizador diretamente (sem 2FA)
        Auth::login($user, $request->boolean('remember'));

        // Regenera a sessao por seguranca (previne session fixation)
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Autenticado com sucesso.',
                'redirect' => '/todo',
            ]);
        }

        return redirect()->intended('/todo');
    }

    // Verifica o codigo de dois fatores enviado pelo utilizador
    // Valida o codigo contra o armazenado na base de dados, verifica a expiracao,
    // e autentica o utilizador caso o codigo seja valido.
    public function verifyTwoFactor(Request $request): JsonResponse|RedirectResponse
    {
        // Valida que o codigo tem exatamente 6 digitos
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        // Recupera o ID do utilizador guardado na sessao durante o login
        $userId = $request->session()->get('auth.pending_2fa_user_id');

        // Se nao existir ID na sessao, a sessao expirou
        if (!$userId) {
            return response()->json([
                'message' => 'A sessao de autenticacao expirou. Inicie sessao novamente.',
            ], 419);
        }

        // Tenta encontrar o utilizador na base de dados
        $user = User::query()->find($userId);

        // Verifica se o utilizador e o codigo existem e sao validos
        if (!$user || !$user->two_factor_enabled || !$user->two_factor_code || !$user->two_factor_expires_at) {
            return response()->json([
                'message' => 'Nao foi possivel validar o codigo.',
            ], 422);
        }

        // Verifica se o codigo ja expirou
        if ($user->two_factor_expires_at->isPast()) {
            // Limpa o codigo expirado da base de dados
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            return response()->json([
                'message' => 'O codigo expirou. Inicie sessao novamente.',
            ], 422);
        }

        // Verifica se o codigo submetido corresponde ao codigo armazenado
        if ($validated['code'] !== $user->two_factor_code) {
            return response()->json([
                'message' => 'Codigo invalido.',
            ], 422);
        }

        // Autentica o utilizador e limpa os dados de 2FA da sessao
        Auth::login($user, (bool) $request->session()->pull('auth.pending_2fa_remember', false));
        $request->session()->forget('auth.pending_2fa_user_id');
        $request->session()->regenerate();

        // Limpa o codigo 2FA da base de dados apos autenticacao bem-sucedida
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Autenticado com sucesso.',
            'redirect' => '/todo',
        ]);
    }

    // Regista um novo utilizador
    // Valida os dados, cria a conta, autentica automaticamente e redireciona para /todo.
    public function register(Request $request): RedirectResponse|JsonResponse
    {
        // Valida nome, email unico e password com confirmacao e minimo de 8 caracteres
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Cria o utilizador e autentica-o automaticamente
        $user = User::create($validated);
        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Conta criada com sucesso.',
                'redirect' => '/todo',
            ], 201);
        }

        return redirect('/todo');
    }

    // Termina a sessao do utilizador autenticado
    // Faz logout, invalida a sessao atual e regenera o token CSRF.
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalida a sessao e regenera o token CSRF por seguranca
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
