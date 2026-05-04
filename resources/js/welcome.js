// Ponto de entrada da landing page (página de boas-vindas)
// Utilizado pela view Blade resources/views/welcome.blade.php
// Monta diretamente o componente WelcomeApp no elemento #app sem roteamento adicional


// createApp: função do Vue 3 que cria uma instância isolada da aplicação
import { createApp } from 'vue';

// WelcomeApp: componente raiz da landing page (hero, funcionalidades, botão de tema, footer)
import WelcomeApp from './components/WelcomeApp.vue';

// Cria a aplicação Vue com WelcomeApp como componente raiz e monta-a no #app do HTML
createApp(WelcomeApp).mount('#app');
