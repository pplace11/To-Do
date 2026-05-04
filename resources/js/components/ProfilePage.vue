<template>
    <!-- Página de perfil do utilizador -->
    <!-- Permite editar nome, email, senha e ativar/desativar autenticação de dois fatores (2FA) -->

    <!-- Contentor de fundo: gradiente azul claro em modo claro; gradiente radial escuro em modo escuro -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 text-slate-900 dark:bg-[radial-gradient(circle_at_top,_#0f172a,_#172554_35%,_#020617_70%)] dark:text-slate-100 p-4 py-8">

        <!-- Cartão principal: largura máxima de 4xl, cantos arredondados, efeito glassmorphism com backdrop-blur -->
        <main class="mx-auto w-full max-w-4xl rounded-3xl border border-white/60 bg-white/80 p-6 shadow-[0_30px_80px_rgba(15,23,42,0.18)] backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/65 dark:shadow-[0_30px_80px_rgba(2,6,23,0.55)] sm:p-8">

            <!-- Cabeçalho da página: título, subtítulo e botão "Voltar" à direita -->
            <div class="flex items-center justify-between gap-4 border-b border-slate-200/70 pb-6 dark:border-slate-700/60">
                <div>
                    <!-- Label superior em maiúsculas (identificador visual da secção) -->
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-blue-600 dark:text-blue-300">Perfil</p>
                    <!-- Título principal da página -->
                    <h1 class="mt-2 text-3xl font-black tracking-tight">Definições da conta</h1>
                    <!-- Descrição resumida das ações disponíveis -->
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Atualize o seu nome, email, senha e autenticação de dois fatores.</p>
                </div>
                <!-- Botão de navegação de regresso à lista de tarefas -->
                <a href="/todo" class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">Voltar</a>
            </div>

            <!-- Grelha de duas colunas em ecrãs grandes: coluna esquerda (formulários) + coluna direita (2FA) -->
            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">

                <!-- Coluna principal: formulários de dados pessoais e de senha -->
                <section class="space-y-6">

                    <!-- Formulário de dados pessoais (nome e email) -->
                    <!-- @submit.prevent evita o reload da página e chama saveProfile() -->
                    <form @submit.prevent="saveProfile" class="rounded-2xl border border-slate-200 bg-transparent p-5 dark:border-slate-700">
                        <h2 class="text-lg font-bold">Dados pessoais</h2>
                        <div class="mt-4 space-y-4">
                            <!-- Campo de nome — ligado a profile.name via v-model -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nome</label>
                                <input v-model="profile.name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                            <!-- Campo de email — ligado a profile.email via v-model -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                                <input v-model="profile.email" type="email" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                        </div>
                        <!-- Botão de submissão: desativado durante o envio; texto muda para feedback visual -->
                        <button type="submit" :disabled="savingProfile" class="mt-5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60">{{ savingProfile ? 'A guardar...' : 'Guardar perfil' }}</button>
                    </form>

                    <!-- Formulário de alteração de senha -->
                    <!-- @submit.prevent evita o reload da página e chama savePassword() -->
                    <form @submit.prevent="savePassword" class="rounded-2xl border border-slate-200 bg-transparent p-5 dark:border-slate-700">
                        <h2 class="text-lg font-bold">Alterar senha</h2>
                        <div class="mt-4 space-y-4">
                            <!-- Campo da senha atual — necessário para validação no servidor -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Senha atual</label>
                                <input v-model="passwordForm.current_password" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                            <!-- Campo da nova senha -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nova senha</label>
                                <input v-model="passwordForm.password" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                            <!-- Campo de confirmação da nova senha — deve coincidir com o campo anterior -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Confirmar nova senha</label>
                                <input v-model="passwordForm.password_confirmation" type="password" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800" required>
                            </div>
                        </div>
                        <!-- Botão de submissão: desativado durante o envio; feedback visual no texto -->
                        <button type="submit" :disabled="savingPassword" class="mt-5 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-60">{{ savingPassword ? 'A atualizar...' : 'Atualizar senha' }}</button>
                    </form>
                </section>

                <!-- Coluna lateral: painel de autenticação de dois fatores (2FA) -->
                <aside class="space-y-6">
                    <section class="rounded-2xl border border-slate-200 bg-transparent p-5 dark:border-slate-700">
                        <h2 class="text-lg font-bold">Autenticação de dois fatores</h2>
                        <!-- Descrição do funcionamento da 2FA nesta aplicação -->
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Quando ativada, ao iniciar sessão será pedido um código enviado para o seu email.</p>

                        <!-- Cartão de estado: mostra se a 2FA está ativa ou inativa + botão de alternância -->
                        <div class="mt-4 flex items-center justify-between gap-4 rounded-2xl bg-slate-50 p-4 dark:bg-slate-800">
                            <div>
                                <p class="text-sm font-semibold">Estado atual</p>
                                <!-- Texto do estado: verde quando ativada, cinzento quando desativada -->
                                <p :class="['text-sm mt-1', profile.two_factor_enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400']">
                                    {{ profile.two_factor_enabled ? 'Ativada' : 'Desativada' }}
                                </p>
                            </div>
                            <!-- Botão de alternância: vermelho para desativar, verde para ativar -->
                            <!-- Desativado durante o pedido em curso; chama toggleTwoFactor() ao clicar -->
                            <button @click="toggleTwoFactor" :disabled="savingTwoFactor" class="rounded-xl px-4 py-2 text-sm font-semibold text-white transition disabled:opacity-60" :class="profile.two_factor_enabled ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                                {{ savingTwoFactor ? 'A atualizar...' : (profile.two_factor_enabled ? 'Desativar' : 'Ativar') }}
                            </button>
                        </div>
                        <!-- Nota informativa sobre o destino do código de verificação -->
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">O código é enviado para o email configurado no perfil.</p>
                    </section>
                </aside>
            </div>

            <!-- Mensagem de erro global: exibida quando errorMessage tem conteúdo -->
            <p v-if="errorMessage" class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">{{ errorMessage }}</p>
            <!-- Mensagem de sucesso global: exibida quando successMessage tem conteúdo -->
            <p v-if="successMessage" class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ successMessage }}</p>
        </main>
    </div>
</template>

<script setup>
// Importações do Vue:
// - onMounted: para carregar os dados do perfil quando o componente é montado
// - reactive: para o objeto de estado do perfil e do formulário de senha
// - ref: para flags de carregamento e mensagens de feedback
import { onMounted, reactive, ref } from 'vue';

// Token CSRF lido do meta tag do HTML — enviado nos cabeçalhos de todos os pedidos mutáveis (PATCH/PUT)
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Flags de estado de envio — desativam os botões enquanto os pedidos estão em curso
const savingProfile = ref(false);    // true enquanto o pedido PATCH /profile está em curso
const savingPassword = ref(false);   // true enquanto o pedido PUT /profile/password está em curso
const savingTwoFactor = ref(false);  // true enquanto o pedido PUT /profile/two-factor está em curso

// Mensagens de feedback exibidas no final da página
const errorMessage = ref('');    // Preenchido quando um pedido falha
const successMessage = ref('');  // Preenchido quando um pedido é bem-sucedido

// Estado reativo do perfil do utilizador
// Preenchido por loadProfile() após a montagem do componente
const profile = reactive({
    name: '',                    // Nome do utilizador
    email: '',                   // Email do utilizador
    two_factor_enabled: false,   // Estado da autenticação de dois fatores
});

// Estado reativo do formulário de alteração de senha
// Limpo automaticamente após uma atualização bem-sucedida
const passwordForm = reactive({
    current_password: '',        // Senha atual (necessária para validação no servidor)
    password: '',                // Nova senha
    password_confirmation: '',   // Confirmação da nova senha (deve coincidir com password)
});

// Carrega os dados do perfil do utilizador a partir do servidor via GET /profile
// Popula o objeto `profile` com os dados devolvidos pela API
async function loadProfile() {
    const response = await fetch('/profile', {
        headers: { Accept: 'application/json' },
    });

    // Lança erro se a resposta não for bem-sucedida (ex.: utilizador não autenticado)
    if (!response.ok) {
        throw new Error('Nao foi possivel carregar o perfil.');
    }

    const data = await response.json();
    profile.name = data.name;
    profile.email = data.email;
    profile.two_factor_enabled = !!data.two_factor_enabled; // Converte para booleano explícito
}

// Limpa ambas as mensagens de feedback antes de cada pedido
// Evita que mensagens de operações anteriores permaneçam visíveis
function resetMessages() {
    errorMessage.value = '';
    successMessage.value = '';
}

// Envia os dados pessoais atualizados (nome e email) para o servidor via PATCH /profile
async function saveProfile() {
    resetMessages();
    savingProfile.value = true; // Bloqueia o botão durante o envio

    try {
        const response = await fetch('/profile', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}), // Inclui CSRF token se disponível
            },
            body: JSON.stringify({ name: profile.name, email: profile.email }),
        });

        const data = await response.json().catch(() => ({})); // Fallback para objeto vazio se o corpo não for JSON
        if (!response.ok) throw new Error(data.message || 'Nao foi possivel atualizar o perfil.');

        successMessage.value = data.message || 'Perfil atualizado com sucesso.';
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao atualizar perfil.';
    } finally {
        savingProfile.value = false; // Liberta o botão independentemente do resultado
    }
}

// Envia o formulário de alteração de senha para o servidor via PUT /profile/password
// Após sucesso, limpa todos os campos do formulário
async function savePassword() {
    resetMessages();
    savingPassword.value = true; // Bloqueia o botão durante o envio

    try {
        const response = await fetch('/profile/password', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}), // Inclui CSRF token se disponível
            },
            body: JSON.stringify(passwordForm), // Envia current_password, password e password_confirmation
        });

        const data = await response.json().catch(() => ({})); // Fallback para objeto vazio se o corpo não for JSON
        if (!response.ok) throw new Error(data.message || 'Nao foi possivel atualizar a senha.');

        // Limpa os campos após atualização bem-sucedida por segurança
        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
        successMessage.value = data.message || 'Senha atualizada com sucesso.';
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao atualizar senha.';
    } finally {
        savingPassword.value = false; // Liberta o botão independentemente do resultado
    }
}

// Alterna o estado da autenticação de dois fatores via PUT /profile/two-factor
// Envia o valor oposto ao estado atual (`!profile.two_factor_enabled`)
async function toggleTwoFactor() {
    resetMessages();
    savingTwoFactor.value = true; // Bloqueia o botão durante o envio

    try {
        const response = await fetch('/profile/two-factor', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}), // Inclui CSRF token se disponível
            },
            body: JSON.stringify({ enabled: !profile.two_factor_enabled }), // Inverte o estado atual
        });

        const data = await response.json().catch(() => ({})); // Fallback para objeto vazio se o corpo não for JSON
        if (!response.ok) throw new Error(data.message || 'Nao foi possivel atualizar a autenticacao de dois fatores.');

        // Atualiza o estado local com o valor confirmado pelo servidor
        profile.two_factor_enabled = !!data.two_factor_enabled;
        successMessage.value = data.message || 'Autenticacao de dois fatores atualizada.';
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao atualizar autenticacao de dois fatores.';
    } finally {
        savingTwoFactor.value = false; // Liberta o botão independentemente do resultado
    }
}

// Ciclo de vida: após a montagem do componente, carrega os dados do perfil do servidor
// Em caso de erro, exibe a mensagem de erro no ecrã
onMounted(async () => {
    resetMessages();
    try {
        await loadProfile();
    } catch (error) {
        errorMessage.value = error.message || 'Erro ao carregar perfil.';
    }
});
</script>
