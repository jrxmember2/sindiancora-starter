# Checklist de Testes

## Ambiente local

- [ ] `composer install`
- [ ] `npm install`
- [ ] `cp .env.example .env`
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate:fresh --seed`
- [ ] `npm run build`

## Smoke test funcional

- [ ] login do superadmin
- [ ] acesso ao dashboard
- [ ] acesso a `superadmin/versions`
- [ ] criacao de empresa
- [ ] criacao de licenca
- [ ] liberacao de modulo
- [ ] troca de empresa
- [ ] criacao de condominio
- [ ] criacao de fornecedor
- [ ] criacao de documento
- [ ] criacao de chamado

## Regras de tenancy

- [ ] usuario sem empresa ativa nao acessa area tenant
- [ ] empresa A nao enxerga dados da empresa B
- [ ] listagens operacionais filtram por `company_id`

## Regras de licenciamento

- [ ] empresa sem licenca ativa recebe bloqueio
- [ ] empresa sem modulo liberado recebe bloqueio
- [ ] limite de condominios impede novos cadastros

## Producao / EasyPanel

- [ ] assets carregando em `https`
- [ ] dominio com SSL valido
- [ ] `storage:link` criado
- [ ] logs sem erro critico apos login
- [ ] superadmin visivel e funcional

## Testes automatizados desejados para proxima etapa

- [ ] autenticacao
- [ ] tenancy
- [ ] licenciamento
- [ ] modulo bloqueado
- [ ] limite de condominios
- [ ] limite de usuarios internos
