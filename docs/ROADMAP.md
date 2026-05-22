# Roadmap

## Status geral

- Fase 0: concluída no código
- Fase 1: concluída no código
- Fase 2: concluída no código
- Fase 3: concluída no código
- Fase 4: concluída no código
- Fase 5: concluída no código
- Fase 5.1: concluída no código
- Fase 6 em diante: não iniciadas

## Fase 0 - Preparação, documentação e arquitetura

Status: concluída

Entregas:

- documentação base formalizada
- arquitetura inicial registrada
- deploy no EasyPanel documentado
- testes mínimos de autenticação e tenancy
- scheduler revisado para produção

## Fase 1 - Base web e UX principal

Status: concluída

Entregas:

- base Laravel + Inertia + React consolidada
- componentes principais do painel
- formulários padronizados
- validações migradas para `Form Requests`
- dashboard e layout autenticado revisados

## Fase 2 - Multiempresa forte

Status: concluída

Entregas:

- empresa ativa resolvida antes do route model binding
- troca de empresa endurecida
- escopo operacional por condomínio preparado
- testes de vazamento entre empresas

## Fase 3 - Licenciamento contratual

Status: concluída

Entregas:

- `license_history`
- `license_usage`
- `LicenseGuard` expandido
- tela `Minha licença`
- bloqueio de módulo e modo somente leitura

## Fase 4 - Usuários e permissões

Status: concluída

Entregas:

- CRUD de usuários internos
- papéis por empresa
- `Policies` e `Gates`
- vínculo `user_condominiums`
- limite de usuários por licença

## Fase 5 - Condomínios

Status: concluída

Entregas:

- cadastro revisado
- upload e remoção de logo
- filtros e indicadores
- limite real de condomínios ativos
- inativação sem perda de dados

## Fase 5.1 - Revisão do tenancy e governança de condomínio

Status: concluída

Objetivo:

Separar plataforma x empresa cliente e preparar condomínios compartilháveis ou transferíveis entre síndicas sem quebrar o isolamento jurídico e operacional.

Entregas:

- [x] separar conceitualmente usuários da plataforma e usuários das empresas clientes
- [x] manter `users.is_superadmin` restrito à equipe da plataforma
- [x] fazer onboarding comercial pelo superadmin com empresa, licença e usuário principal
- [x] adicionar troca obrigatória de senha no primeiro acesso do admin master
- [x] consolidar o papel de admin master da empresa sem transformá-lo em superadmin da plataforma
- [x] evoluir condomínio para registro canônico por documento
- [x] criar estrutura de vínculo empresa-condomínio com papéis `principal` e `solidaria`
- [x] criar fluxo de conflito por documento duplicado com notificação e trilha auditável
- [x] permitir decisões `transferir`, `mesclar` e `recusar`
- [x] permitir override do superadmin com transferência forçada
- [x] revisar scopes, guards, políticas e queries para respeitar vínculo ativo empresa-condomínio
- [x] registrar a nova direção arquitetural na documentação

Critérios de aceite atendidos:

- superadmin mantém visão macro da plataforma
- admin master da empresa cliente administra apenas a própria empresa
- cadastro duplicado por documento não cria condomínio duplicado silenciosamente
- cada condomínio pode ter empresa principal e empresas solidárias autorizadas
- transferência, mescla e recusa geram notificação, histórico e auditoria
- a base ficou preparada para troca de síndico sem misturar automaticamente dados privados da empresa anterior

## Fase 6 - Categorias e fornecedores

Status: próxima fase

Objetivo:

Criar os cadastros auxiliares que sustentam os módulos operacionais.

Entregas planejadas:

- CRUD completo de categorias por tipo
- endurecimento do CRUD de fornecedores
- filtros, busca e exportação simples

## Fase 7 - Chamados

Status: planejada

Objetivo:

Completar o módulo principal de chamados, agora já respeitando o vínculo ativo empresa-condomínio.

## Fase 8 - Acompanhamentos

Status: planejada

Objetivo:

Criar a timeline operacional do chamado com separação entre visibilidade interna e pública.

## Fase 9 - Documentos

Status: planejada

Objetivo:

Completar documentos com upload real, download seguro, vencimentos e regra de transferência entre gestões.

## Fase 10 - Dashboard e home operacional

Status: planejada

Objetivo:

Criar indicadores clicáveis por empresa e condomínio.

## Fase 11 - Relatórios

Status: planejada

Objetivo:

Criar relatórios operacionais e base para PDF.

## Fase 12 - Cronograma

Status: planejada

Objetivo:

Criar a visão em calendário da operação.

## Fase 13 - Manutenções

Status: planejada

Objetivo:

Criar o módulo de manutenções preventivas e corretivas.

## Fase 14 - Obras

Status: planejada

Objetivo:

Criar o módulo de obras.

## Fase 15 - Pagamentos

Status: planejada

Objetivo:

Criar o módulo de pagamentos com recorrência e parcelas.

## Fase 16 - Orçamentos

Status: planejada

Objetivo:

Criar o módulo de orçamentos vinculado à operação.

## Fase 17 - Preferências da empresa

Status: planejada

Objetivo:

Criar parâmetros globais da empresa e preparar preferências futuras por condomínio.

## Fase 18 - Auditoria e logs

Status: planejada

Objetivo:

Completar a trilha de auditoria da plataforma e das empresas.

## Fase 19 - Notificações

Status: planejada

Objetivo:

Criar inbox interno e notificações por e-mail.

## Fase 20 - WhatsApp

Status: planejada

Objetivo:

Preparar integração com WhatsApp respeitando licença e governança.

## Fase 21 - IA

Status: planejada

Objetivo:

Adicionar recursos de IA com controle de crédito e consumo por licença.

## Fase 22 - App do condômino

Status: planejada

Objetivo:

Preparar API e iniciar o app mobile do condômino.

## Fase 23 - Deploy EasyPanel

Status: planejada

Objetivo:

Endurecer o deploy final com worker, scheduler, logs e backup.

## Fase 24 - Testes, segurança e produção

Status: planejada

Objetivo:

Fechar os testes críticos, revisão de segurança, performance e readiness comercial.
