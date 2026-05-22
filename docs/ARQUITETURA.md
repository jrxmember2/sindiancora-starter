# Arquitetura

## Objetivo da arquitetura

Criar uma base segura para um SaaS multiempresa de gestão condominial, com separação clara entre operação, licenciamento, tenant e autorização.

## Stack principal

- Laravel 12
- PostgreSQL
- Redis
- React 18
- Inertia.js 2
- TailwindCSS 3
- Vite

## Visão de alto nível

O sistema é um monolito modular.

- backend HTTP, regras de negócio, validações e persistência ficam no Laravel
- frontend do painel fica em React + Inertia
- banco principal fica no PostgreSQL
- fila, cache e sessão podem usar Redis
- deploy é executado no EasyPanel

## Princípios arquiteturais

- toda entidade privada da empresa precisa de `company_id`
- nenhuma consulta operacional deve confiar apenas em `id`
- controllers devem ser finos
- regras de negócio devem sair do controller ao evoluir o projeto
- autorização deve ficar centralizada em `Policies/Gates`
- validação deve ir para `Form Requests`
- módulos precisam ser bloqueados em frontend, rota e backend
- entidades operacionais ligadas ao condomínio devem evoluir para validar vínculo ativo com o condomínio, e não apenas `company_id`

## Camadas atuais

- `app/Http/Controllers`: fluxo web
- `app/Http/Middleware`: superadmin, tenant, licença e módulo
- `app/Models`: entidades de domínio
- `app/Services/Licensing`: regras iniciais de licenciamento
- `app/Services/Permissions`: abilities por empresa
- `resources/js/Layouts`: shells do painel e autenticação
- `resources/js/Pages`: telas por contexto funcional
- `config/`: configurações da aplicação
- `database/migrations`: contrato do banco
- `database/seeders`: bootstrap de módulos e superadmin

## Estrutura alvo de evolução

Ao longo das próximas fases, a base deve convergir para algo próximo disto:

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

## Estratégia de multiempresa

### Base atual

- `SetCurrentCompany` define a empresa ativa em sessão
- `BelongsToCompany` adiciona `CompanyScope`
- `CompanyScope` injeta filtro por `company_id` quando existe empresa ativa
- `company_users` vincula usuário, empresa, papel e flags adicionais
- `user_condominiums` já prepara escopo por condomínio para usuários internos

### Regras obrigatórias

- usuário não superadmin sempre opera dentro de uma empresa ativa
- toda entidade privada da empresa deve herdar ou reproduzir o comportamento tenant-aware
- em módulos com escopo por condomínio, o filtro precisa considerar empresa ativa e condomínios permitidos

### Evolução planejada

- existem dois níveis claros de administração: plataforma e empresa cliente
- `users.is_superadmin` deve ficar restrito ao time da plataforma
- o usuário principal do cliente deve ser o admin master da empresa, sem receber poderes de superadmin da plataforma
- dados privados da empresa continuam presos a `company_id`
- dados operacionais do condomínio devem evoluir para um modelo de registro canônico + vínculos ativos entre empresa e condomínio
- o tenancy futuro precisa validar não apenas `company_id`, mas também o vínculo ativo da empresa com o condomínio acessado

## Plataforma x empresa cliente

### Plataforma

- superadmin vê tudo
- superadmin cria empresa, licença e usuário principal do cliente
- usuários do time da Serratech ficam separados conceitualmente dos usuários das empresas clientes

### Empresa cliente

- o usuário principal da empresa é o admin master do tenant
- ele enxerga apenas os dados da própria empresa
- ele cria condomínios, usuários internos e demais cadastros conforme a licença liberada

## Condomínios compartilhados e transição de gestão

### Conceito registrado

- um condomínio pode começar com uma empresa administradora principal
- em casos raros, outra empresa pode solicitar acesso ao mesmo condomínio por CNPJ
- essa tentativa não deve criar um segundo condomínio duplicado
- a empresa principal atual deve poder decidir entre transferir, mesclar ou recusar
- o superadmin da plataforma pode resolver o conflito manualmente

### Direção de modelagem

- manter um registro canônico do condomínio identificado por documento/CNPJ
- separar o vínculo empresa-condomínio em tabela própria, com papel, status e datas
- distinguir dados privados da empresa de dados operacionais do condomínio
- preparar trilha auditável para solicitações, decisões e transferências

### Implicações arquiteturais

- nem todo dado operacional poderá continuar dependente apenas de `company_id`
- chamados, documentos, manutenções, obras e demais módulos ligados ao condomínio devem migrar para regras baseadas em vínculo ativo com o condomínio
- itens comerciais, usuários internos, agenda privada e observações sensíveis da empresa anterior não devem migrar automaticamente em uma transferência
- essa revisão precisa acontecer antes de aprofundar os módulos operacionais das próximas fases

## Estratégia de licenciamento

### Base atual

- `licenses`
- `modules`
- `license_modules`
- `LicenseGuard`
- middlewares `EnsureLicenseIsActive` e `EnsureModuleIsEnabled`

### Meta

- controlar acesso a módulo
- controlar limite de condomínios ativos
- controlar limite de usuários internos
- controlar storage, IA e WhatsApp
- registrar histórico e consumo

## Estratégia de permissões

### Base atual

- superadmin por `users.is_superadmin`
- perfil base por `company_users.role`
- flags adicionais em `company_users`

### Meta

- policies por agregado
- gates para ações críticas
- permissão por módulo e por ação
- vínculo usuário x condomínio
- distinção clara entre permissões da plataforma e permissões do tenant

## Estratégia de frontend

- Inertia para navegação entre telas do painel
- componentes React reutilizáveis
- layout autenticado e layout de autenticação separados
- branding próprio da plataforma
- bloqueio visual de módulos sem liberar acesso real pelo backend

## Estratégia de observabilidade

- logs de aplicação via Laravel
- histórico de versões visível para superadmin
- auditoria funcional em fases posteriores
- monitoramento de fila, cron e falhas em produção

## Riscos técnicos atuais

- o tenancy compartilhado por condomínio ainda não foi implementado
- parte do domínio operacional ainda pressupõe `company_id` como fronteira suficiente
- uploads reais de documentos ainda não foram endurecidos
- scheduler ainda não deve ser ativado sem revisar `job_batches`
- bundle frontend está grande e vai precisar de code splitting depois
