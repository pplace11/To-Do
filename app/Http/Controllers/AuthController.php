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
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Credenciais invalidas.',
                    'errors' => [
                        'email' => ['Credenciais invalidas.'],
                    ],
                ], 422);
            }

            return back()->withErrors([
                'email' => 'Credenciais invalidas.',
            ])->onlyInput('email');
        }

        if ($user->two_factor_enabled) {
            $code = (string) random_int(100000, 999999);

            $user->two_factor_code = $code;
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            Mail::raw("O seu codigo de autenticacao de dois fatores e: {$code}. Este codigo expira em 10 minutos.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Codigo de autenticacao - To-Do App');
            });

            $request->session()->put('auth.pending_2fa_user_id', $user->id);
            $request->session()->put('auth.pending_2fa_remember', $request->boolean('remember'));

            return response()->json([
                'message' => 'Enviamos um codigo de verificacao para o seu email.',
                'requires_two_factor' => true,
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Autenticado com sucesso.',
                'redirect' => '/todo',
            ]);
        }

        return redirect()->intended('/todo');
    }

    public function verifyTwoFactor(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('auth.pending_2fa_user_id');

        if (! $userId) {
            return response()->json([
                'message' => 'A sessao de autenticacao expirou. Inicie sessao novamente.',
            ], 419);
        }

        $user = User::query()->find($userId);

        if (! $user || ! $user->two_factor_enabled || ! $user->two_factor_code || ! $user->two_factor_expires_at) {
            return response()->json([
                'message' => 'Nao foi possivel validar o codigo.',
            ], 422);
        }

        if ($user->two_factor_expires_at->isPast()) {
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            return response()->json([
                'message' => 'O codigo expirou. Inicie sessao novamente.',
            ], 422);
        }

        if ($validated['code'] !== $user->two_factor_code) {
            return response()->json([
                'message' => 'Codigo invalido.',
            ], 422);
        }

        Auth::login($user, (bool) $request->session()->pull('auth.pending_2fa_remember', false));
        $request->session()->forget('auth.pending_2fa_user_id');
        $request->session()->regenerate();

        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Autenticado com sucesso.',
            'redirect' => '/todo',
        ]);
    }

    public function register(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

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

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
