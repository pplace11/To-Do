#!/usr/bin/env bash

# =============================================================================
# render-build.sh — Script de build executado pelo Render.com antes do deploy
#
# Este script é invocado automaticamente pela plataforma Render durante o
# processo de build (antes de iniciar o servidor). Executa, por ordem:
#   1. Instalação das dependências PHP (Composer)
#   2. Instalação das dependências Node e compilação dos assets frontend (Vite)
#   3. Cache de configuração, rotas e views do Laravel (otimização de performance)
#   4. Criação do link simbólico do disco público (storage:link)
#   5. Execução das migrações pendentes na base de dados de produção
# =============================================================================

# Ativa o modo "exit on error": se qualquer comando retornar um código de saída
# diferente de zero, o script termina imediatamente e o deploy falha.
# Evita que erros silenciosos deixem a aplicação num estado inconsistente.
set -o errexit

# ─── Dependências PHP ────────────────────────────────────────────────────────
echo "Installing Composer dependencies..."
# Instala apenas as dependências de produção (exclui PHPUnit, Pest, etc.)
# --no-dev             : omite o bloco require-dev do composer.json
# --optimize-autoloader: gera o classmap otimizado para carregamento mais rápido em produção
composer install --no-dev --optimize-autoloader

# ─── Assets frontend (Node / Vite) ───────────────────────────────────────────
echo "Installing Node dependencies and building assets..."
# Instala todas as dependências Node declaradas no package.json
# (usa npm install em vez de npm ci pois o Render pode não ter package-lock.json em cache)
npm install
# Compila os assets Vue/CSS com Vite para produção:
# minificação, tree-shaking, hashing de nomes de ficheiros → resultado em public/build/
npm run build

# ─── Cache do Laravel (otimização de arranque) ───────────────────────────────
echo "Caching Laravel config, routes and views..."
# Compila toda a configuração num único ficheiro PHP — evita leitura de múltiplos .env e config/*.php em cada pedido
php artisan config:cache
# Compila todas as rotas num único ficheiro — evita re-processar routes/*.php em cada pedido
php artisan route:cache
# Pré-compila todas as views Blade para PHP — evita compilação on-the-fly em produção
php artisan view:cache

# ─── Link simbólico do disco público ─────────────────────────────────────────
# Cria o link simbólico public/storage → storage/app/public
# necessário para servir ficheiros do disco público via URL
# --force : recria o link mesmo que já exista (útil em re-deploys)
# || true : ignora o erro de saída se o link já existir e --force não conseguir substituir;
#           garante que o script não falha por causa desta operação não crítica
php artisan storage:link --force || true

# ─── Migrações da base de dados ──────────────────────────────────────────────
echo "Running migrations..."
# Executa todas as migrações pendentes na base de dados de produção
# --force : necessário em ambiente de produção (o Laravel pede confirmação por defeito)
#           sem esta flag o comando falharia num ambiente não interativo como o Render
php artisan migrate --force
