# Licenciamento

## Modelo comercial

SindiAncora nao trabalha com planos publicos fixos.

Cada empresa cliente recebe uma licenca contratual personalizada, configurada manualmente pelo superadmin.

## O que a licenca controla

- quantidade de condominios ativos permitidos
- quantidade de usuarios internos permitidos
- modulos habilitados
- vigencia
- status da licenca
- status financeiro
- possibilidade de excedente
- bloqueio de novos cadastros ao atingir limite
- modo somente leitura quando expirar
- suspensao automatica por inadimplencia

## Estrutura atual

- `licenses`
- `modules`
- `license_modules`
- `App\Services\Licensing\LicenseGuard`

## Regras atuais implementadas

- `isActive`
- `canAccessModule`
- `canCreateCondominium`
- `canCreateInternalUser`
- `usage`

## Regras que precisam entrar nas proximas fases

- `canUseStorage`
- `canUseAI`
- `canUseWhatsApp`
- historico de alteracoes de licenca
- medicao persistida de consumo
- alertas de limite proximo

## Camadas de bloqueio obrigatorias

### 1. Frontend

- esconder item de menu
- mostrar estado travado quando fizer sentido

### 2. Rota

- middleware `module:{key}`
- middleware de licenca ativa

### 3. Backend

- `LicenseGuard`
- services e actions que negam execucao mesmo com URL forcada

## Diretriz operacional

Licenca nao substitui permissao.

Mesmo com modulo liberado, o usuario ainda precisa de permissao funcional para executar a acao.

## Evolucao planejada

- `license_history`
- `license_usage`
- tela "Minha Licenca"
- notas e anexos contratuais
- alerta de renovacao e inadimplencia
