# Roadmap

## Status geral

- Fase 0: em formalizacao
- Fase 1: parcial
- Fase 2: parcial
- Fase 3: parcial
- Fase 4 em diante: nao iniciadas

## Fase 0 - Preparacao, documentacao e arquitetura

### Objetivo

Endurecer a base do projeto antes da expansao funcional.

### Entregas da fase

- README raiz reescrito
- `docs/README_PROJETO.md`
- arquitetura formalizada
- banco inicial documentado
- licenciamento documentado
- permissoes documentadas
- deploy no EasyPanel documentado
- backlog por fase organizado
- checklist de testes inicial

### Backlog executavel

- [x] revisar o estado real do projeto
- [x] corrigir login em producao via proxy/https
- [x] aplicar branding inicial
- [x] criar versionamento visivel apenas para superadmin
- [x] formalizar documentacao base
- [ ] criar testes minimos de autenticacao
- [ ] criar testes minimos de tenancy
- [ ] revisar scheduler e fila para producao
- [ ] padronizar textos sem ruido de encoding legado

### Criterios de aceite

- login funcionando em producao
- build frontend funcionando
- docs base atualizadas
- direcao arquitetural definida

## Fase 1 - Base web e UX principal

### Objetivo

Consolidar a fundacao do painel web.

### Backlog executavel

- [ ] completar componentes base faltantes
- [ ] revisar dashboard inicial
- [ ] padronizar formularios
- [ ] mover validacoes de controller para `Form Requests`
- [ ] adicionar toasts/notificacoes visuais
- [ ] revisar responsividade

## Fase 2 - Multiempresa forte

### Objetivo

Garantir isolamento seguro entre tenants.

### Backlog executavel

- [ ] endurecer troca de empresa
- [ ] criar testes de vazamento entre empresas
- [ ] revisar queries operacionais por `company_id`
- [ ] preparar `user_condominiums`

## Fase 3 - Licenciamento contratual

### Objetivo

Completar o coracao comercial do SaaS.

### Backlog executavel

- [ ] `license_history`
- [ ] `license_usage`
- [ ] completar `LicenseGuard`
- [ ] tela de uso da licenca
- [ ] alertas de limite

## Fase 4 - Usuarios e permissoes

- [ ] CRUD de usuarios internos
- [ ] roles por empresa
- [ ] policies e gates
- [ ] vinculo usuario x condominio

## Fase 5 - Condominios

- [ ] completar cadastro
- [ ] revisar limites ativos/inativos
- [ ] preparar upload de logo

## Fase 6 - Categorias e fornecedores

- [ ] CRUD completo de categorias
- [ ] endurecer fornecedor por tenant
- [ ] filtros e exportacao simples

## Fase 7 - Chamados

- [ ] detalhes completos
- [ ] responsavel e fornecedor
- [ ] prazos e indicadores
- [ ] anexos

## Fase 8 - Acompanhamentos

- [ ] timeline do chamado
- [ ] historico de status
- [ ] historico de responsavel

## Fase 9 - Documentos

- [ ] upload real
- [ ] download seguro
- [ ] vencimentos e status

## Fase 10 - Dashboard e home operacional

- indicadores por empresa e condominio
- cards clicaveis
- leitura de uso e pendencias

## Fase 11 - Relatorios

- relatorios por periodo
- base para PDF
- controle de conteudo publico x interno

## Fase 12 - Cronograma

- calendario mensal
- itens por dia
- navegacao para o detalhe

## Fase 13 - Manutencoes

- preventivas e corretivas
- recorrencia
- relatorio

## Fase 14 - Obras

- acompanhamento de obras
- anexos
- status e custos

## Fase 15 - Pagamentos

- vencimentos
- recorrencia
- parcelas
- notificacoes internas

## Fase 16 - Orcamentos

- origem do orcamento
- aprovacao
- historico

## Fase 17 - Preferencias da empresa

- parametros operacionais
- configuracoes do app futuro
- configuracoes de WhatsApp

## Fase 18 - Auditoria e logs

- trilha de alteracoes
- tela para superadmin
- tela limitada para empresa

## Fase 19 - Notificacoes

- inbox interno
- e-mail
- preferencias por usuario

## Fase 20 - WhatsApp

- configuracao de instancia
- horario de atendimento
- historico de conversa

## Fase 21 - IA

- correcoes de texto
- sugestoes e resumos
- controle de credito

## Fase 22 - App do condomino

- API preparada
- decisao Flutter x Expo antes da implementacao
- funcionalidades iniciais do app

## Fase 23 - Deploy EasyPanel

- endurecimento final de deploy
- worker, scheduler e backup
- checklist operacional

## Fase 24 - Testes, seguranca e producao

- testes criticos
- revisao de performance
- monitoramento
- readiness para venda

## Regras de execucao

Em cada fase:

1. definir objetivo
2. listar arquivos alterados
3. implementar
4. validar build e migrations
5. revisar seguranca e tenant
6. atualizar documentacao
7. registrar a release
