# SindiAncora

SindiAncora é uma plataforma SaaS multiempresa para gestão condominial, com licenciamento contratual personalizado por cliente e governança preparada para operação compartilhada ou transferência de condomínios entre empresas.

## Objetivo do projeto

Construir um sistema próprio para operação condominial, sem copiar identidade, layout, código ou textos de terceiros, com foco em:

- multiempresa com isolamento forte
- licenciamento contratual flexível
- módulos habilitados por licença
- governança de condomínio com empresa principal e empresa solidária
- base preparada para chamados, documentos, fornecedores, manutenções, obras, pagamentos, IA e app do condômino

## Estado atual da base

Release atual: `0.7.0 - Tenant Governance`

O projeto já entrega:

- autenticação web
- superadmin da plataforma
- usuários da plataforma separados dos usuários internos das empresas clientes
- onboarding comercial com empresa, licença e admin master
- troca obrigatória de senha no primeiro acesso
- dashboard inicial
- CRUD de empresas
- CRUD de licenças
- catálogo de módulos
- CRUD de condomínios com logo, filtros, indicadores e inativação sem perda de dados
- CRUD inicial de fornecedores
- CRUD inicial de documentos
- CRUD inicial de chamados
- versionamento visível apenas para superadmin
- tenancy com empresa ativa em sessão
- escopo por condomínio para usuários internos
- `LicenseGuard` com controle de uso e alertas
- políticas e gates para usuários e condomínios
- governança de condomínio com:
  - registro canônico por documento
  - vínculos `principal` e `solidaria`
  - solicitação por duplicidade de CNPJ
  - decisões `mesclar`, `transferir` e `recusar`
  - override do superadmin com transferência forçada

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

## Instalação local

### 1. Dependências

Você precisa de:

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
php artisan storage:link
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
php artisan test
php artisan storage:link
npm run dev
npm run build
```

## Estrutura de pastas

```txt
app/
  Http/
  Models/
  Notifications/
  Policies/
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

## Variáveis de ambiente principais

```env
APP_NAME="SindiAncora"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_VERSION=0.7.0
APP_RELEASE_NAME="Tenant Governance"
APP_RELEASE_STAGE=production
APP_RELEASED_AT=2026-05-22
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

O deploy de produção é feito no EasyPanel com App Service + PostgreSQL e, depois da estabilização da base, Redis, worker e scheduler.

Guia detalhado:

- [docs/DEPLOY_EASYPANEL.md](docs/DEPLOY_EASYPANEL.md)

## Documentação

- [docs/README_PROJETO.md](docs/README_PROJETO.md)
- [docs/ARQUITETURA.md](docs/ARQUITETURA.md)
- [docs/BANCO_DE_DADOS.md](docs/BANCO_DE_DADOS.md)
- [docs/LICENCIAMENTO.md](docs/LICENCIAMENTO.md)
- [docs/MODULOS.md](docs/MODULOS.md)
- [docs/PERMISSOES.md](docs/PERMISSOES.md)
- [docs/ROADMAP.md](docs/ROADMAP.md)
- [docs/CHECKLIST_TESTES.md](docs/CHECKLIST_TESTES.md)
- [docs/PROMPTS_FASES.md](docs/PROMPTS_FASES.md)

## Próximo passo recomendado

Com a Fase 5.1 concluída, a prioridade agora é a **Fase 6: categorias e fornecedores**, seguida pela evolução do módulo de chamados.
