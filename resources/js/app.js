// Ponto de entrada principal da aplicação Vue
// Responsável por determinar qual componente montar com base no URL atual
// Utiliza roteamento manual por pathname em vez de vue-router,
// pois cada rota é servida pelo Laravel e o Vue apenas hidrata o #app correspondente

import { createApp } from 'vue';

// Importação de todos os componentes de página disponíveis na aplicação
import ToDoApp from './components/ToDoApp.vue';         // Painel principal de tarefas (/todo)
import WelcomeApp from './components/WelcomeApp.vue';   // Landing page pública (/)
import TaskCreatePage from './components/TaskCreatePage.vue'; // Formulário de nova tarefa (/todo/nova)
import LoginPage from './components/LoginPage.vue';     // Página de login (/login)
import RegisterPage from './components/RegisterPage.vue'; // Página de registo (/register)
import ProfilePage from './components/ProfilePage.vue'; // Página de perfil do utilizador (/perfil)

// Normaliza o pathname atual:
// - remove barras finais (replace(/\/+$/, '')) para evitar falsos negativos (ex: '/todo/' !== '/todo')
// - fallback para '/' caso o pathname fique vazio após a remoção
const path = window.location.pathname.replace(/\/+$/, '') || '/';

// Componente padrão: WelcomeApp (landing page para rotas não mapeadas ou raiz "/")
let AppComponent = WelcomeApp;

// Seleciona o componente correto com base no pathname normalizado
if (path === '/todo') {
	AppComponent = ToDoApp;           // Painel de tarefas
} else if (path === '/todo/nova') {
	AppComponent = TaskCreatePage;    // Criação de nova tarefa
} else if (path === '/login') {
	AppComponent = LoginPage;         // Autenticação
} else if (path === '/register') {
	AppComponent = RegisterPage;      // Registo de novo utilizador
} else if (path === '/perfil') {
	AppComponent = ProfilePage;       // Gestão do perfil e 2FA
}

// Monta o componente selecionado no elemento #app do HTML
// O elemento #app é renderizado pelo Blade (resources/views/welcome.blade.php ou todo.blade.php)
createApp(AppComponent).mount('#app');
