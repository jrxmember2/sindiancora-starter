# SindiÂncora — Starter SaaS Condominial

Starter inicial para um SaaS multiempresa/multicondomínio com **Laravel + PostgreSQL + Redis + React/Inertia**.

## O que já vem neste pontapé inicial

- Base Laravel/Inertia/React.
- Autenticação web.
- Layout limpo, moderno, responsivo, com cards, curvas, traços finos e navegação lateral.
- Estrutura SaaS multiempresa.
- Licenciamento contratual personalizado, sem planos fixos.
- Módulos liberáveis por licença.
- `LicenseGuard` para validar licença, módulos e limites.
- Migrations iniciais.
- Seed dos módulos.
- Seed do Superadmin.
- CRUD inicial de Empresas.
- CRUD inicial de Licenças.
- Listagem inicial de Módulos.
- CRUD inicial/base de Condomínios, Fornecedores, Documentos e Chamados.
- Documentação em `/docs`.
- Guia de deploy no EasyPanel para `https://sindiancora.serratech.tec.br`.

## Importante

Este starter **não inclui `vendor/` nem `node_modules/`**. Depois de subir ao GitHub, rode no seu ambiente:

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## Usuário inicial

O seed usa:

```env
SUPERADMIN_NAME="Junior Amorim"
SUPERADMIN_EMAIL="admin@sindiancora.local"
SUPERADMIN_PASSWORD="password"
```

Altere antes de colocar em produção.

## Docker local opcional

```bash
docker compose up -d postgres redis
```

## Próximo passo recomendado

1. Subir este ZIP no GitHub.
2. Criar o GitHub Project com milestones por fase.
3. Pedir ao Codex para iniciar pela Fase 1 refinando setup, testes e autenticação.
4. Não pular para módulos avançados antes de estabilizar multiempresa/licença.
