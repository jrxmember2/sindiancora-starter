# SindiAncora

SindiAncora é uma plataforma SaaS multiempresa para gestão condominial, com licenciamento contratual personalizado por cliente.

O projeto usa Laravel no backend, PostgreSQL como banco principal, Redis para fila/cache/sessão quando habilitado, e React + Inertia no painel web.

## Objetivo do projeto

Construir um sistema próprio para operação condominial, sem cópia de identidade, layout, código ou textos de terceiros, com foco em:

- multiempresa com isolamento por `company_id`
- licenciamento contratual flexível
- módulos habilitados por licença
- operação de chamados, documentos, fornecedores e gestão futura de pagamentos, obras, manutenções e app do condômino

## Estado atual

A base atual já entrega:

- autenticação web
- dashboard inicial revisado
- superadmin
- CRUD inicial de empresas
- CRUD inicial de licenças
- catálogo de módulos
- CRUD inicial de condomínios
- CRUD inicial de fornecedores
- CRUD inicial de documentos
- CRUD inicial de chamados
- versionamento visível apenas para superadmin
- base de tenant com `currentCompany`, `BelongsToCompany` e `CompanyScope`
- `LicenseGuard` e middlewares iniciais de licença e módulo
- `Form Requests` nos fluxos principais
- componentes base do painel para tabela, drawer, modal, confirmação e toast
- endurecimento da troca de empresa e do route model binding tenant-aware
- preparação de `user_condominiums` para escopo operacional por condomínio
- histórico contratual de licenças em banco
- snapshots de uso da licença por empresa
- tela "Minha licença" com status, alertas, limites e módulos liberados
- gestão de usuários internos com papéis, vínculo por condomínio, gates, policies e logs iniciais

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
npm run dev
npm run build
```

## Estrutura de pastas

```txt
app/
  Http/
  Models/
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

APP_VERSION=0.5.0
APP_RELEASE_NAME="User Access Control"
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

Com a Fase 4 concluída, a prioridade agora é:

1. completar o módulo de condomínios
2. revisar limites de ativos x inativos
3. preparar upload de logo
4. ampliar a matriz de autorização para os demais módulos
