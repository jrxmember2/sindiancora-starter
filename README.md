# SindiAncora

SindiAncora e uma plataforma SaaS multiempresa para gestao condominial, com licenciamento contratual personalizado por cliente.

O projeto usa Laravel no backend, PostgreSQL como banco principal, Redis para fila/cache/sessao quando habilitado, e React + Inertia no painel web.

## Objetivo do projeto

Construir um sistema proprio para operacao condominial, sem copia de identidade, layout, codigo ou textos de terceiros, com foco em:

- multiempresa com isolamento por `company_id`
- licenciamento contratual flexivel
- modulos habilitados por licenca
- operacao de chamados, documentos, fornecedores e gestao futura de pagamentos, obras, manutencoes e app do condomino

## Estado atual

A base atual ja entrega:

- autenticacao web
- dashboard inicial revisado
- superadmin
- CRUD inicial de empresas
- CRUD inicial de licencas
- catalogo de modulos
- CRUD inicial de condominios
- CRUD inicial de fornecedores
- CRUD inicial de documentos
- CRUD inicial de chamados
- versionamento visivel apenas para superadmin
- base de tenant com `currentCompany`, `BelongsToCompany` e `CompanyScope`
- `LicenseGuard` e middlewares iniciais de licenca e modulo
- `Form Requests` nos fluxos principais
- componentes base do painel para tabela, drawer, modal, confirmacao e toast
- endurecimento da troca de empresa e do route model binding tenant-aware
- preparacao de `user_condominiums` para escopo operacional por condominio
- historico contratual de licencas em banco
- snapshots de uso da licenca por empresa
- tela "Minha licenca" com status, alertas, limites e modulos liberados

## Stack

- PHP 8.3
- Laravel 12
- PostgreSQL
- Redis
- React 18
- Inertia.js 2
- TailwindCSS 3
- Vite
- Docker / EasyPanel

## Instalacao local

### 1. Dependencias

Voce precisa de:

- PHP 8.3+
- Composer 2
- Node.js 18+
- PostgreSQL 16+
- Redis 7+

Se preferir, pode usar Docker apenas para PostgreSQL e Redis:

```bash
docker compose up -d postgres redis
```

### 2. Preparar ambiente

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
```

### 3. Rodar em desenvolvimento

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

## Comandos principais

```bash
php artisan migrate --seed
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan optimize
npm run dev
npm run build
```

## Estrutura de pastas

```txt
app/
  Http/
  Models/
  Providers/
  Services/
bootstrap/
config/
database/
docs/
public/
resources/
  css/
  js/
    Components/
    Layouts/
    Pages/
routes/
tests/
```

## Variaveis de ambiente principais

```env
APP_NAME="SindiAncora"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_VERSION=0.4.0
APP_RELEASE_NAME=Contract Licensing
APP_RELEASE_STAGE=production
APP_RELEASED_AT=2026-05-21
APP_BUILD_SHA=

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sindiancora
DB_USERNAME=sindiancora
DB_PASSWORD=sindiancora

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

SUPERADMIN_NAME="Junior Amorim"
SUPERADMIN_EMAIL="admin@sindiancora.local"
SUPERADMIN_PASSWORD="password"
```

## Deploy

O deploy de producao e feito no EasyPanel com App Service + PostgreSQL e, depois da estabilizacao da base, Redis, worker e scheduler.

Guia detalhado:

- [docs/DEPLOY_EASYPANEL.md](docs/DEPLOY_EASYPANEL.md)

## Documentacao

- [docs/README_PROJETO.md](docs/README_PROJETO.md)
- [docs/ARQUITETURA.md](docs/ARQUITETURA.md)
- [docs/BANCO_DE_DADOS.md](docs/BANCO_DE_DADOS.md)
- [docs/LICENCIAMENTO.md](docs/LICENCIAMENTO.md)
- [docs/MODULOS.md](docs/MODULOS.md)
- [docs/PERMISSOES.md](docs/PERMISSOES.md)
- [docs/ROADMAP.md](docs/ROADMAP.md)
- [docs/CHECKLIST_TESTES.md](docs/CHECKLIST_TESTES.md)
- [docs/PROMPTS_FASES.md](docs/PROMPTS_FASES.md)

## Proximo passo recomendado

Com a Fase 3 concluida, a prioridade agora e:

1. usuarios internos por empresa
2. roles, policies e gates
3. vinculo usuario x condominio
4. endurecimento de autorizacao por modulo e acao
