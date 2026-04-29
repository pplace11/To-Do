import { createApp } from 'vue';
import ToDoApp from './components/ToDoApp.vue';
import WelcomeApp from './components/WelcomeApp.vue';
import TaskCreatePage from './components/TaskCreatePage.vue';
import LoginPage from './components/LoginPage.vue';
import RegisterPage from './components/RegisterPage.vue';

const path = window.location.pathname.replace(/\/+$/, '') || '/';
let AppComponent = WelcomeApp;

if (path === '/todo') {
	AppComponent = ToDoApp;
} else if (path === '/todo/nova') {
	AppComponent = TaskCreatePage;
} else if (path === '/login') {
	AppComponent = LoginPage;
} else if (path === '/register') {
	AppComponent = RegisterPage;
}

createApp(AppComponent).mount('#app');
