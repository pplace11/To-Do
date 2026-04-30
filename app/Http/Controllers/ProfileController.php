<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($validated);

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

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();
        $user->password = $validated['password'];
        $user->save();

        return response()->json([
            'message' => 'Senha atualizada com sucesso.',
        ]);
    }

    public function updateTwoFactor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $user->two_factor_enabled = $validated['enabled'];

        if (! $validated['enabled']) {
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
        }

        $user->save();

        return response()->json([
            'message' => $validated['enabled']
                ? 'Autenticação de dois fatores ativada com sucesso.'
                : 'Autenticação de dois fatores desativada com sucesso.',
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
        ]);
    }
}
