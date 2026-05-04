# =============================================================================
# Dockerfile — Imagem de produção da aplicação To-Do (Laravel + Vue + Vite)
#
# Utiliza uma build multi-stage para separar responsabilidades:
#   Stage 1 (node_build): compila os assets frontend com Node/Vite
#   Stage 2 (imagem final): imagem PHP de produção, copia apenas o necessário
#
# A abordagem multi-stage mantém a imagem final leve, sem Node.js nem
# dependências de desenvolvimento — apenas o resultado compilado é copiado.
# =============================================================================

# ── Stage 1: Node – compilação dos assets frontend ───────────────────────────
# Usa Node 20 Alpine (imagem mínima) para compilar os assets Vue/Vite.
# Este stage é descartado no final — apenas a pasta public/build é preservada.
FROM node:20-alpine AS node_build

# Define o diretório de trabalho dentro do container
WORKDIR /app

# Copia primeiro os ficheiros de manifesto de dependências (package.json e package-lock.json)
# para aproveitar o cache de layers do Docker: se não mudarem, o `npm ci` não é re-executado
COPY package*.json ./

# Instala as dependências npm de forma determinística (usa package-lock.json)
# `npm ci` é preferível a `npm install` em CI/CD por ser mais rápido e reprodutível
RUN npm ci

# Copia o restante do código fonte (recursos Vue, CSS, configuração Vite, etc.)
COPY . .

# Compila os assets para produção (minificação, hashing de nomes de ficheiros, etc.)
# O resultado é colocado em public/build/ conforme configurado no vite.config.js
RUN npm run build

# ── Stage 2: PHP – imagem de produção ────────────────────────────────────────
# Usa PHP 8.4 CLI em Alpine (imagem base mínima sem servidor web embutido).
# O servidor de desenvolvimento do Laravel (`artisan serve`) é usado para servir a app.
FROM php:8.4-cli-alpine

# Instala dependências do sistema operativo e extensões PHP necessárias em produção
RUN apk add --no-cache \
        bash \
        curl \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
        zip \
    && docker-php-ext-install \
        bcmath \
        mbstring \
        pdo \
        pdo_pgsql \
        zip

# Copia o binário do Composer diretamente da imagem oficial do Composer 2
# Evita instalar o Composer manualmente e garante sempre a versão mais recente do branch 2.x
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho da aplicação Laravel dentro do container
WORKDIR /var/www

# Copia todo o código fonte PHP da aplicação para o container
COPY . .

# Copia os assets frontend já compilados do Stage 1 (node_build)
# Apenas a pasta public/build/ é necessária — o Node.js não é incluído na imagem final
COPY --from=node_build /app/public/build ./public/build

# Instala as dependências PHP via Composer em modo produção:
#   --no-dev             : exclui dependências de desenvolvimento (PHPUnit, Pest, etc.)
#   --optimize-autoloader: gera o classmap otimizado para melhor performance
#   --no-interaction     : evita prompts interativos durante a instalação
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Cria as pastas necessárias ao funcionamento do Laravel em runtime e define permissões:
#   storage/framework/{cache,sessions,views}: cache de framework, sessões e views compiladas
#   bootstrap/cache: cache de configuração e rotas gerada pelo artisan
#   chmod 775: leitura/escrita pelo proprietário e grupo; leitura pelo mundo
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && chmod -R 775 storage bootstrap/cache

# Declara a porta exposta pelo container (usada pelo `artisan serve` no CMD abaixo)
EXPOSE 8080

# Comando de arranque do container em produção:
#   config:cache  — compila toda a configuração num único ficheiro para performance
#   route:cache   — compila todas as rotas num único ficheiro para performance
#   view:cache    — pré-compila todas as views Blade
#   migrate --force — executa as migrações pendentes (--force aceita em produção sem confirmação)
#   serve         — inicia o servidor HTTP do Laravel em todas as interfaces na porta 8080
CMD php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=8080
