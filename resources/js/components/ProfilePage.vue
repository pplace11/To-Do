<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 text-slate-900 dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100 p-4 py-8">
        <main class="mx-auto w-full max-w-4xl rounded-3xl border border-white/60 bg-white/80 p-6 shadow-[0_30px_80px_rgba(15,23,42,0.18)] backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/65 dark:shadow-[0_30px_80px_rgba(2,6,23,0.55)] sm:p-8">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200/70 pb-6 dark:border-slate-700/60">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-blue-600 dark:text-blue-300">Perfil</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight">Definições da conta</h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Atualize o seu nome, email, senha e autenticação de dois fatores.</p>
                </div>
                <a href="/todo" class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">Voltar</a>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="space-y-6">
                    <form @submit.prevent="saveProfile" class="rounded-2xl border border-slate-200 bg-transparent p-5 dark:border-slate-700">
                        <h2 class="text-lg font-bold">Dados pessoais</h2>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nome</label>
                                <input v-model="profile.name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                                <input v-model="profile.email" type="email" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                        </div>
                        <button type="submit" :disabled="savingProfile" class="mt-5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60">{{ savingProfile ? 'A guardar...' : 'Guardar perfil' }}</button>
                    </form>

                    <form @submit.prevent="savePassword" class="rounded-2xl border border-slate-200 bg-transparent p-5 dark:border-slate-700">
                        <h2 class="text-lg font-bold">Alterar senha</h2>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Senha atual</label>
                                <input v-model="passwordForm.current_password" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nova senha</label>
                                <input v-model="passwordForm.password" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Confirmar nova senha</label>
                                <input v-model="passwordForm.password_confirmation" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                        </div>
                        <button type="submit" :disabled="savingPassword" class="mt-5 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-60">{{ savingPassword ? 'A atualizar...' : 'Atualizar senha' }}</button>
                    </form>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-2xl border border-slate-200 bg-transparent p-5 dark:border-slate-700">
                        <h2 class="text-lg font-bold">Autenticação de dois fatores</h2>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Quando ativada, ao iniciar sessão será pedido um código enviado para o seu email.</p>
                        <div class="mt-4 flex items-center justify-between gap-4 rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                            <div>
                                <p class="text-sm font-semibold">Estado atual</p>
                                <p :class="['text-sm mt-1', profile.two_factor_enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400']">
                                    {{ profile.two_factor_enabled ? 'Ativada' : 'Desativada' }}
                                </p>
                            </div>
                            <button @click="toggleTwoFactor" :disabled="savingTwoFactor" class="rounded-xl px-4 py-2 text-sm font-semibold text-white transition disabled:opacity-60" :class="profile.two_factor_enabled ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                                {{ savingTwoFactor ? 'A atualizar...' : (profile.two_factor_enabled ? 'Desativar' : 'Ativar') }}
                            </button>
                        </div>
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">O código é enviado para o email configurado no perfil.</p>
                    </section>
                </aside>
            </div>

            <p v-if="errorMessage" class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">{{ errorMessage }}</p>
            <p v-if="successMessage" class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ successMessage }}</p>
        </main>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const savingProfile = ref(false);
const savingPassword = ref(false);
const savingTwoFactor = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const profile = reactive({
    name: '',
    email: '',
    two_factor_enabled: false,
});

const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
});

async function loadProfile() {
    const response = await fetch('/profile', {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error('Nao foi possivel carregar o perfil.');
    }

    const data = await response.json();
    profile.name = data.name;
    profile.email = data.email;
    profile.two_factor_enabled = !!data.two_factor_enabled;
}

function resetMessages() {
    errorMessage.value = '';
    successMessage.value = '';
}

async function saveProfile() {
    resetMessages();
    savingProfile.value = true;

    try {
        const response = await fetch('/profile', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify({ name: profile.name, email: profile.email }),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Nao foi possivel atualizar o perfil.');

        successMessage.value = data.message || 'Perfil atualizado com sucesso.';
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao atualizar perfil.';
    } finally {
        savingProfile.value = false;
    }
}

async function savePassword() {
    resetMessages();
    savingPassword.value = true;

    try {
        const response = await fetch('/profile/password', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify(passwordForm),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Nao foi possivel atualizar a senha.');

        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
        successMessage.value = data.message || 'Senha atualizada com sucesso.';
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao atualizar senha.';
    } finally {
        savingPassword.value = false;
    }
}

async function toggleTwoFactor() {
    resetMessages();
    savingTwoFactor.value = true;

    try {
        const response = await fetch('/profile/two-factor', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            body: JSON.stringify({ enabled: !profile.two_factor_enabled }),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Nao foi possivel atualizar a autenticacao de dois fatores.');

        profile.two_factor_enabled = !!data.two_factor_enabled;
        successMessage.value = data.message || 'Autenticacao de dois fatores atualizada.';
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao atualizar autenticacao de dois fatores.';
    } finally {
        savingTwoFactor.value = false;
    }
}

onMounted(async () => {
    resetMessages();
    try {
        await loadProfile();
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao carregar perfil.';
    }
});
</script>
