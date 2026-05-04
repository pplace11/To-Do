<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// Controller responsavel pela gestao do perfil do utilizador autenticado.
// Inclui metodos para visualizar, atualizar dados pessoais, alterar a senha
// e gerir a autenticacao de dois fatores.
class ProfileController extends Controller
{
    // Retorna os dados do perfil do utilizador autenticado em formato JSON.
    // Inclui: id, nome, email e estado do 2FA.
    public function show(Request $request): JsonResponse
    {
        // Obtem o utilizador autenticado a partir da requisicao
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
        ]);
    }

    // Atualiza o nome e o email do utilizador autenticado.
    // O email deve ser unico na tabela de utilizadores, ignorando o proprio utilizador.
    public function update(Request $request): JsonResponse
    {
        // Obtem o utilizador autenticado
        $user = $request->user();

        // Valida nome e email; o email deve ser unico exceto para o proprio utilizador
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        // Atualiza os dados do utilizador na base de dados
        $user->update($validated);

        // Retorna os dados atualizados do utilizador
        return response()->json([
            'message' => 'Perfil atualizado com sucesso.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
            ],
        ]);
    }

    // Atualiza a senha do utilizador autenticado.
    // Exige a senha atual para confirmacao e a nova senha com campo de confirmacao.
    public function updatePassword(Request $request): JsonResponse
    {
        // Valida a senha atual e a nova senha (minimo 8 caracteres, com confirmacao)
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Atualiza a senha do utilizador autenticado
        $user = $request->user();
        $user->password = $validated['password'];
        $user->save();

        return response()->json([
            'message' => 'Senha atualizada com sucesso.',
        ]);
    }

    // Ativa ou desativa a autenticacao de dois fatores para o utilizador autenticado.
    // Quando desativada, limpa tambem o codigo 2FA e a data de expiracao armazenados.
    public function updateTwoFactor(Request $request): JsonResponse
    {
        // Valida que o campo 'enabled' e um booleano
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        // Atualiza o estado do 2FA no utilizador
        $user = $request->user();
        $user->two_factor_enabled = $validated['enabled'];

        // Se o 2FA for desativado, limpa os dados do codigo pendente
        if (! $validated['enabled']) {
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
        }

        $user->save();

        // Retorna mensagem consoante o estado final do 2FA
        return response()->json([
            'message' => $validated['enabled']
                ? 'Autenticação de dois fatores ativada com sucesso.'
                : 'Autenticação de dois fatores desativada com sucesso.',
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
        ]);
    }
}
