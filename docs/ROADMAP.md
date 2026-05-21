# Roadmap

## Status geral

- Fase 0: concluida no codigo
- Fase 1: concluida no codigo
- Fase 2: concluida no codigo
- Fase 3: concluida no codigo
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
- [x] criar testes minimos de autenticacao
- [x] criar testes minimos de tenancy
- [x] revisar scheduler e fila para producao
- [x] padronizar textos sem ruido de encoding legado

### Criterios de aceite

- login funcionando em producao
- build frontend funcionando
- docs base atualizadas
- direcao arquitetural definida

## Fase 1 - Base web e UX principal

### Objetivo

Consolidar a fundacao do painel web.

### Backlog executavel

- [x] completar componentes base faltantes
- [x] revisar dashboard inicial
- [x] padronizar formularios
- [x] mover validacoes de controller para `Form Requests`
- [x] adicionar toasts/notificacoes visuais
- [x] revisar responsividade

### Criterios de aceite

- login funcional
- dashboard carregando
- navegacao responsiva
- formularios principais padronizados
- build frontend validado
- testes basicos PHP passando

## Fase 2 - Multiempresa forte

### Objetivo

Garantir isolamento seguro entre tenants.

### Backlog executavel

- [x] endurecer troca de empresa
- [x] criar testes de vazamento entre empresas
- [x] revisar queries operacionais por `company_id`
- [x] preparar `user_condominiums`

### Criterios de aceite

- empresa ativa resolvida antes do route model binding
- usuario comum nao troca para empresa inativa, suspensa ou sem vinculo ativo
- chamados e documentos respeitam company_id e, quando houver, escopo por condominio
- testes de tenancy cobrindo URL direta, troca de empresa e escopo por condominio

## Fase 3 - Licenciamento contratual

### Objetivo

Completar o coracao comercial do SaaS.

### Backlog executavel

- [x] `license_history`
- [x] `license_usage`
- [x] completar `LicenseGuard`
- [x] tela de uso da licenca
- [x] alertas de limite
- [x] bloquear escrita em modo somente leitura
- [x] testes de contrato e bloqueio de modulo

### Criterios de aceite

- superadmin cria e atualiza licencas com historico registrado
- uso da licenca e sincronizado por empresa
- empresa consegue visualizar contrato, modulos e limites em "Minha licenca"
- modulos bloqueados nao podem ser acessados por URL direta
- licenca em modo somente leitura permite consulta e bloqueia escrita

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
