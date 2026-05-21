# Permissões

Perfis planejados:

- Superadmin
- Admin da empresa
- Gestor
- Operacional
- Financeiro
- Condômino

Base existente:

- `users.is_superadmin`
- `company_users.role`
- `company_users.can_access_whatsapp`
- `company_users.only_responsible_issues`

Próximo passo: implementar Policies e matriz granular por módulo/ação.
