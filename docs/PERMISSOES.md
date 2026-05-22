# Permissões

## Perfis planejados

- Superadmin
- Admin da empresa
- Gestor
- Operacional
- Financeiro
- Condômino

## Base atual implementada

- `users.is_superadmin`
- `company_users.role`
- `company_users.status`
- `company_users.can_access_whatsapp`
- `company_users.only_responsible_issues`
- `config/company_permissions.php`
- `CompanyPermissionService`
- `CompanyUserPolicy`
- gates para `view/create/update/deactivate` usuários internos
- escopo `user_condominiums`

## Estado atual

Hoje a plataforma já separa:

- acesso total do superadmin
- acesso autenticado comum
- acesso por empresa ativa
- acesso por licença ativa
- acesso por módulo liberado
- acesso por ability na gestão de usuários internos
- acesso por condomínio permitido
- limitação opcional para ver apenas chamados atribuídos

Ainda faltam:

- matriz granular por ação para os módulos operacionais restantes
- policies específicas para fornecedores, documentos, categorias e financeiro
- tela administrativa de auditoria por permissão
- preferências de notificação por usuário

## Matriz atual da Fase 4

### Superadmin

- gerencia tenants
- gerencia licenças
- gerencia módulos
- visualiza versões da plataforma
- ignora empresa ativa para rotinas administrativas

### Admin da empresa

- gerencia usuários internos
- vincula usuários a condomínios
- visualiza uso da licença
- opera módulos liberados

### Gestor

- opera módulos liberados com maior amplitude
- não gerencia usuários internos nesta fase

### Operacional

- opera módulos liberados
- pode ser limitado a chamados atribuídos

### Financeiro

- perfil reservado para fases financeiras
- ainda sem matriz de ações própria

### Condômino

- previsto para fase posterior via app/API
- não consome limite de usuário interno

## Abilities implementadas

- `view_company_users`
- `create_company_users`
- `update_company_users`
- `deactivate_company_users`
- `assign_user_condominiums`

## Regras obrigatórias

- permissão nunca substitui licença
- licença nunca substitui permissão
- tenant nunca deve ser inferido só pelo frontend
- em módulos por condomínio, validar condomínio permitido ao usuário
- vínculos inativos não podem autenticar nem trocar empresa

## Próximos entregáveis

- policies por agregado operacional
- matriz por módulo/ação além da área de usuários
- autorização financeira específica
- tela de consulta de logs por papel
