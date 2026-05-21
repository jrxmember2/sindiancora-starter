# Permissoes

## Perfis planejados

- Superadmin
- Admin da empresa
- Gestor
- Operacional
- Financeiro
- Condomino

## Base atual implementada

- `users.is_superadmin`
- `company_users.role`
- `company_users.status`
- `company_users.can_access_whatsapp`
- `company_users.only_responsible_issues`

## Estado atual

Hoje a plataforma ja separa:

- acesso total do superadmin
- acesso autenticado comum
- acesso por empresa ativa
- acesso por licenca ativa
- acesso por modulo liberado

Ainda faltam:

- `Policies`
- `Gates`
- permissao por acao
- permissao por condominio
- permissao por recurso financeiro

## Matriz alvo

### Superadmin

- gerencia tenants
- gerencia licencas
- gerencia modulos
- visualiza versoes da plataforma
- nao deve depender de empresa ativa para rotinas administrativas

### Admin da empresa

- gerencia usuarios internos
- gerencia condominios
- visualiza uso da licenca
- opera modulos liberados

### Gestor

- opera modulos liberados com maior amplitude
- consulta indicadores e operacao

### Operacional

- atua em chamados e tarefas permitidas
- pode ser limitado a itens atribuidos

### Financeiro

- acessa rotinas financeiras liberadas por modulo

### Condomino

- previsto para fase posterior via app/API
- nao consome limite de usuario interno

## Regras obrigatorias

- permissao nunca substitui licenca
- licenca nunca substitui permissao
- tenant nunca deve ser inferido so pelo frontend
- em modulos por condominio, validar condominio permitido ao usuario

## Proximos entregaveis

- `Form Requests` para validacao
- `Policies` por agregado
- tabela de vinculo `user_condominiums`
- tela de gestao de usuarios internos
- matriz por modulo/acao
