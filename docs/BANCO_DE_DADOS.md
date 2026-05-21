# Banco de Dados

## Banco principal

- SGBD: PostgreSQL
- timezone de referencia: `America/Sao_Paulo`

## Principio central

Toda tabela operacional deve carregar `company_id`.

Nenhuma consulta operacional deve depender apenas do `id` da linha. Sempre validar tenant, licenca, permissao e, quando aplicavel, acesso ao condominio.

## Tabelas atuais

### Base do framework

- `users`
- `cache`
- `jobs`
- `failed_jobs`

### SaaS / tenancy / licenciamento

- `companies`
- `company_users`
- `modules`
- `licenses`
- `license_modules`

### Operacao inicial

- `condominiums`
- `suppliers`
- `categories`
- `issues`
- `issue_updates`
- `documents`
- `audit_logs`
- `settings`

## Tabelas planejadas para as proximas fases

- `user_condominiums`
- `license_history`
- `license_usage`
- anexos de chamados
- anexos de documentos estruturados
- relatorios gerados
- notificacoes
- pagamentos
- orcamentos
- manutencoes
- obras

## Entidades centrais

### companies

Representa o tenant comercial.

Campos relevantes:

- `name`
- `slug`
- `status`
- `primary_color`
- `secondary_color`

### company_users

Pivot entre usuario e empresa.

Campos relevantes:

- `role`
- `status`
- `can_access_whatsapp`
- `only_responsible_issues`

### licenses

Contrato operacional e comercial da empresa.

Campos relevantes:

- `status`
- `financial_status`
- `billing_type`
- `max_condominiums`
- `max_internal_users`
- `max_storage_mb`
- `max_whatsapp_instances`
- `monthly_ai_credits`
- `allow_overage`
- `block_new_records_on_limit`
- `read_only_when_expired`
- `auto_suspend_when_overdue`

### modules e license_modules

Catalogo de modulos e habilitacao por licenca.

### condominiums

Cadastro principal por empresa cliente.

### issues

Nucleo operacional inicial do sistema.

## Indices e cuidados

Ja existem indices basicos em status e relacionamentos, mas ao evoluir o projeto devemos reforcar:

- indices por `company_id`
- indices compostos por `company_id` + status
- indices por data em entidades operacionais
- indices por `company_id` + `condominium_id` quando houver filtros frequentes

## Regras de modelagem

- nao remover migrations antigas
- toda alteracao contratual deve entrar em migration nova
- nao apagar dados de tenant por exclusao simples
- preferir inativacao ou cancelamento logico quando a regra de negocio pedir historico
