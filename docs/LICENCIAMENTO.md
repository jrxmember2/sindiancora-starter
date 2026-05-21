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
- `license_history`
- `license_usage`
- `App\Services\Licensing\LicenseGuard`
- `App\Services\Licensing\LicenseHistoryService`
- `App\Services\Licensing\LicenseUsageService`

## Regras atuais implementadas

- `isActive`
- `canAccessModule`
- `canCreateCondominium`
- `canCreateInternalUser`
- `canUseStorage`
- `canUseAI`
- `canUseWhatsApp`
- `usage`
- `alerts`
- `status`
- bloqueio de escrita em modo somente leitura
- tela "Minha Licenca"

## Regras que precisam entrar nas proximas fases

- anexos contratuais
- workflow de aprovacao comercial
- conciliacao financeira automatica
- consumo real de storage, WhatsApp e IA pelos modulos futuros

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

- notas e anexos contratuais
- alerta de renovacao e inadimplencia
- workflows comerciais e financeiros
- consumo real de storage, WhatsApp e IA alimentado pelos modulos operacionais
