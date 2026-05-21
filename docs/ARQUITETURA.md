# Arquitetura

## Objetivo da arquitetura

Criar uma base segura para um SaaS multiempresa de gestao condominial, com separacao clara entre operacao, licenciamento, tenant e autorizacao.

## Stack principal

- Laravel 12
- PostgreSQL
- Redis
- React 18
- Inertia.js 2
- TailwindCSS 3
- Vite

## Visao de alto nivel

O sistema e um monolito modular.

- backend HTTP, regras de negocio, validacoes e persistencia ficam no Laravel
- frontend do painel fica em React + Inertia
- banco principal fica no PostgreSQL
- fila, cache e sessao podem usar Redis
- deploy e executado no EasyPanel

## Principios arquiteturais

- toda entidade operacional precisa de `company_id`
- nenhuma consulta operacional deve confiar apenas em `id`
- controllers devem ser finos
- regras de negocio devem sair do controller ao evoluir o projeto
- autorizacao deve ficar centralizada em `Policies/Gates`
- validacao deve ir para `Form Requests`
- modulos precisam ser bloqueados em frontend, rota e backend

## Camadas atuais

- `app/Http/Controllers`: fluxo web
- `app/Http/Middleware`: superadmin, tenant, licenca e modulo
- `app/Models`: entidades de dominio
- `app/Services/Licensing`: regras iniciais de licenciamento
- `resources/js/Layouts`: shells do painel e autenticacao
- `resources/js/Pages`: telas por contexto funcional
- `config/`: configuracoes da aplicacao
- `database/migrations`: contrato do banco
- `database/seeders`: bootstrap de modulos e superadmin

## Estrutura alvo de evolucao

Ao longo das proximas fases, a base deve convergir para algo proximo disto:

```txt
app/
  Actions/
  Domain/
    Auth/
    Tenancy/
    Licensing/
    Users/
    Condominiums/
    Suppliers/
    Issues/
    Documents/
    Reports/
  Http/
    Controllers/
    Middleware/
    Requests/
  Notifications/
  Policies/
  Providers/
  Services/
  Support/
resources/js/
  Components/
  Layouts/
  Pages/
tests/
  Feature/
  Unit/
```

## Estrategia de multiempresa

Base atual:

- `SetCurrentCompany` define a empresa ativa em sessao
- `BelongsToCompany` adiciona `CompanyScope`
- `CompanyScope` injeta filtro por `company_id` quando existe empresa ativa

Regras obrigatorias:

- usuario nao superadmin sempre opera dentro de uma empresa ativa
- toda entidade operacional deve herdar ou reproduzir o comportamento tenant-aware
- em modulos com escopo por condominio, o filtro deve considerar `company_id` e condominios permitidos

## Estrategia de licenciamento

Base atual:

- `licenses`
- `modules`
- `license_modules`
- `LicenseGuard`
- middlewares `EnsureLicenseIsActive` e `EnsureModuleIsEnabled`

Meta:

- controlar acesso a modulo
- controlar limite de condominios ativos
- controlar limite de usuarios internos
- controlar storage, IA e WhatsApp
- registrar historico e consumo

## Estrategia de permissoes

Base atual:

- superadmin por `users.is_superadmin`
- perfil base por `company_users.role`
- flags adicionais em `company_users`

Meta:

- policies por agregado
- gates para acoes criticas
- permissao por modulo e por acao
- vinculo usuario x condominio

## Estrategia de frontend

- Inertia para navegacao entre telas do painel
- componentes React reutilizaveis
- layout autenticado e layout de autenticacao separados
- branding propio da plataforma
- bloqueio visual de modulos sem liberar acesso real pelo backend

## Estrategia de observabilidade

- logs de aplicacao via Laravel
- historico de versoes visivel para superadmin
- auditoria funcional em fases posteriores
- monitoramento de fila, cron e falhas em producao

## Riscos tecnicos atuais

- ausencia de testes automatizados
- controller ainda concentra validacoes em varios pontos
- scheduler ainda nao deve ser ativado sem ajustar `job_batches`
- upload e storage ainda nao foram endurecidos
- bundle frontend esta grande e vai precisar de code splitting depois
