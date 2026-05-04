# ✅ To-Do App

To-Do App e uma aplicacao web em Laravel para gestao de tarefas pessoais, com autenticação, perfil de utilizador, autenticação de dois fatores, filtros, calendario, partilha de tarefas e interface responsiva em Vue.

---

## 📋 Funcionalidades

### 📝 Gestao de Tarefas
- ✅ Criacao, consulta, edicao e remocao de tarefas
- 🏷️ Definicao de prioridade alta, media e baixa
- 📅 Configuracao de data de vencimento para organizacao das tarefas
- ✔️ Atualizacao do estado entre pendente e concluida

### 🔎 Organizacao e Visualizacao
- 📂 Filtros por estado, prioridade e data
- 📆 Vista de calendario para acompanhar tarefas planeadas
- 📋 Alternancia entre vista em lista e grelha
- 📊 Resumo com total de tarefas, pendentes e concluidas

### 👤 Autenticacao e Perfil
- 🔐 Registo e login de utilizadores
- ✉️ Autenticacao de dois fatores por codigo enviado por email
- 🙍 Gestao de perfil com atualizacao de nome, email e palavra-passe
- 🚪 Encerramento de sessao com protecao de rotas autenticadas

### 🤝 Colaboracao e Experiencia
- 📤 Partilha de tarefas com outros utilizadores por email
- 🔔 Notificacoes visuais para operacoes de sucesso e erro
- 🌗 Suporte a tema claro e escuro
- 📱 Interface adaptada para desktop e dispositivos moveis

## 🛠️ Tecnologias Utilizadas

- Laravel 13
- PHP 8.3+
- Vue 3
- Tailwind CSS 4
- Vite 8
- Pest
- SQLite ou MySQL

## ⚙️ Como Executar o Projeto

### ✅ Pre-requisitos
Certifique-se de ter instalado PHP 8.3+, Composer, Node.js, npm e uma base de dados configurada no ficheiro `.env`.

### 1️⃣ Clonar o repositorio
```bash
git clone <url-do-repositorio>
cd To-Do
```

### 2️⃣ Instalacao e configuracao automatica
Use o script de setup para instalar dependencias, criar o `.env` caso necessario, gerar a chave da aplicacao, executar migrations e compilar os assets:
```bash
composer run setup
```

### 3️⃣ Configuracao manual
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### 4️⃣ Executar o ambiente de desenvolvimento
```bash
composer run dev
```

### 5️⃣ Aceder a aplicacao
Abra http://localhost:8000

## 🧪 Testes

```bash
composer test
```

Ou, se preferir:

```bash
php artisan test
```

## 📁 Estrutura do Projeto

```text
📁 To-Do/
├── 📄 artisan
├── 📄 composer.json
├── 📄 package.json
├── 📄 phpunit.xml
├── 📄 README.md
├── 📄 vite.config.js
├── 📁 app/
│   ├── 📁 Http/
│   │   └── 📁 Controllers/
│   │       ├── 📄 AuthController.php
│   │       ├── 📄 ProfileController.php
│   │       └── 📄 TaskController.php
│   ├── 📁 Models/
│   │   ├── 📄 Task.php
│   │   └── 📄 User.php
│   ├── 📁 Policies/
│   │   └── 📄 TaskPolicy.php
│   └── 📁 Providers/
├── 📁 bootstrap/
├── 📁 config/
├── 📁 database/
│   ├── 📁 factories/
│   ├── 📁 migrations/
│   └── 📁 seeders/
├── 📁 public/
│   └── 📁 build/
├── 📁 resources/
│   ├── 📁 css/
│   ├── 📁 js/
│   │   ├── 📄 app.js
│   │   ├── 📄 welcome.js
│   │   └── 📁 components/
│   │       ├── 📄 CalendarView.vue
│   │       ├── 📄 LoginPage.vue
│   │       ├── 📄 ProfilePage.vue
│   │       ├── 📄 RegisterPage.vue
│   │       ├── 📄 ShareTaskModal.vue
│   │       ├── 📄 TaskCreatePage.vue
│   │       ├── 📄 TaskEditModal.vue
│   │       ├── 📄 TaskForm.vue
│   │       ├── 📄 TaskList.vue
│   │       ├── 📄 ToDoApp.vue
│   │       └── 📄 WelcomeApp.vue
│   └── 📁 views/
│       ├── 📁 auth/
│       ├── 📄 todo.blade.php
│       └── 📄 welcome.blade.php
├── 📁 routes/
│   ├── 📄 console.php
│   ├── 📄 tasks.php
│   └── 📄 web.php
├── 📁 storage/
├── 📁 tests/
│   ├── 📄 Pest.php
│   ├── 📄 TestCase.php
│   ├── 📁 Feature/
│   │   └── 📄 TaskTest.php
│   └── 📁 Unit/
│       └── 📄 TaskModelTest.php
└── 📁 vendor/
```

## 📝 Observacoes

- A interface principal da aplicacao carrega a SPA Vue dentro das views Blade.
- O sistema inclui autenticacao de dois fatores com envio de codigo por email.
- As rotas de tarefas estao protegidas por autenticacao e devolvem respostas JSON.
- Existe suporte para partilha de tarefas entre utilizadores autenticados.

## 👥 Autor

Desenvolvido no contexto do projeto Inovcorp.
