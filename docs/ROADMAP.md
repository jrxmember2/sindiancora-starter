# Roadmap

## Status geral

- Fase 0: concluída no código
- Fase 1: concluída no código
- Fase 2: concluída no código
- Fase 3: concluída no código
- Fase 4: concluída no código
- Fase 5 em diante: não iniciadas

## Fase 0 - Preparação, documentação e arquitetura

### Objetivo

Endurecer a base do projeto antes da expansão funcional.

### Entregas da fase

- README raiz reescrito
- `docs/README_PROJETO.md`
- arquitetura formalizada
- banco inicial documentado
- licenciamento documentado
- permissões documentadas
- deploy no EasyPanel documentado
- backlog por fase organizado
- checklist de testes inicial

### Backlog executável

- [x] revisar o estado real do projeto
- [x] corrigir login em produção via proxy/https
- [x] aplicar branding inicial
- [x] criar versionamento visível apenas para superadmin
- [x] formalizar documentação base
- [x] criar testes mínimos de autenticação
- [x] criar testes mínimos de tenancy
- [x] revisar scheduler e fila para produção
- [x] padronizar textos sem ruído de encoding legado

### Critérios de aceite

- login funcionando em produção
- build frontend funcionando
- docs base atualizadas
- direção arquitetural definida

## Fase 1 - Base web e UX principal

### Objetivo

Consolidar a fundação do painel web.

### Backlog executável

- [x] completar componentes base faltantes
- [x] revisar dashboard inicial
- [x] padronizar formulários
- [x] mover validações de controller para `Form Requests`
- [x] adicionar toasts/notificações visuais
- [x] revisar responsividade

### Critérios de aceite

- login funcional
- dashboard carregando
- navegação responsiva
- formulários principais padronizados
- build frontend validado
- testes básicos PHP passando

## Fase 2 - Multiempresa forte

### Objetivo

Garantir isolamento seguro entre tenants.

### Backlog executável

- [x] endurecer troca de empresa
- [x] criar testes de vazamento entre empresas
- [x] revisar queries operacionais por `company_id`
- [x] preparar `user_condominiums`

### Critérios de aceite

- empresa ativa resolvida antes do route model binding
- usuário comum não troca para empresa inativa, suspensa ou sem vínculo ativo
- chamados e documentos respeitam `company_id` e, quando houver, escopo por condomínio
- testes de tenancy cobrindo URL direta, troca de empresa e escopo por condomínio

## Fase 3 - Licenciamento contratual

### Objetivo

Completar o coração comercial do SaaS.

### Backlog executável

- [x] `license_history`
- [x] `license_usage`
- [x] completar `LicenseGuard`
- [x] tela de uso da licença
- [x] alertas de limite
- [x] bloquear escrita em modo somente leitura
- [x] testes de contrato e bloqueio de módulo

### Critérios de aceite

- superadmin cria e atualiza licenças com histórico registrado
- uso da licença é sincronizado por empresa
- empresa consegue visualizar contrato, módulos e limites em "Minha licença"
- módulos bloqueados não podem ser acessados por URL direta
- licença em modo somente leitura permite consulta e bloqueia escrita

## Fase 4 - Usuários e permissões

### Objetivo

Criar a gestão de usuários internos da empresa com papéis, permissões e escopo por condomínio.

### Backlog executável

- [x] CRUD de usuários internos
- [x] papéis por empresa com config central de abilities
- [x] policies e gates para a área de usuários
- [x] vínculo `user_condominiums` com tela de seleção
- [x] validação de limite de usuários internos pela licença
- [x] compartilhamento de abilities para o frontend
- [x] limitação opcional de chamados para vínculos marcados como "somente atribuídos"
- [x] logs iniciais de criação, edição e inativação em `audit_logs`

### Critérios de aceite

- admin da empresa cria usuários internos até o limite contratado
- usuário sem permissão não acessa a gestão de usuários via menu, URL nem backend
- vínculo sem condomínios marcados enxerga todos os condomínios ativos da empresa
- vínculo com condomínios marcados só enxerga o escopo permitido
- usuário operacional com flag de chamados atribuídos vê apenas a própria fila
- suíte PHP cobre gestão de usuários, tenancy e restrições principais

## Fase 5 - Condomínios

- [ ] completar cadastro
- [ ] revisar limites ativos/inativos
- [ ] preparar upload de logo

## Fase 6 - Categorias e fornecedores

- [ ] CRUD completo de categorias
- [ ] endurecer fornecedor por tenant
- [ ] filtros e exportação simples

## Fase 7 - Chamados

- [ ] detalhes completos
- [ ] responsável e fornecedor
- [ ] prazos e indicadores
- [ ] anexos

## Fase 8 - Acompanhamentos

- [ ] timeline do chamado
- [ ] histórico de status
- [ ] histórico de responsável

## Fase 9 - Documentos

- [ ] upload real
- [ ] download seguro
- [ ] vencimentos e status

## Fase 10 - Dashboard e home operacional

- indicadores por empresa e condomínio
- cards clicáveis
- leitura de uso e pendências

## Fase 11 - Relatórios

- relatórios por período
- base para PDF
- controle de conteúdo público x interno

## Fase 12 - Cronograma

- calendário mensal
- itens por dia
- navegação para o detalhe

## Fase 13 - Manutenções

- preventivas e corretivas
- recorrência
- relatório

## Fase 14 - Obras

- acompanhamento de obras
- anexos
- status e custos

## Fase 15 - Pagamentos

- vencimentos
- recorrência
- parcelas
- notificações internas

## Fase 16 - Orçamentos

- origem do orçamento
- aprovação
- histórico

## Fase 17 - Preferências da empresa

- parâmetros operacionais
- configurações do app futuro
- configurações de WhatsApp

## Fase 18 - Auditoria e logs

- trilha de alterações
- tela para superadmin
- tela limitada para empresa

## Fase 19 - Notificações

- inbox interno
- e-mail
- preferências por usuário

## Fase 20 - WhatsApp

- configuração de instância
- horário de atendimento
- histórico de conversa

## Fase 21 - IA

- correções de texto
- sugestões e resumos
- controle de crédito

## Fase 22 - App do condômino

- API preparada
- decisão Flutter x Expo antes da implementação
- funcionalidades iniciais do app

## Fase 23 - Deploy EasyPanel

- endurecimento final de deploy
- worker, scheduler e backup
- checklist operacional

## Fase 24 - Testes, segurança e produção

- testes críticos
- revisão de performance
- monitoramento
- readiness para venda

## Regras de execução

Em cada fase:

1. definir objetivo
2. listar arquivos alterados
3. implementar
4. validar build e migrations
5. revisar segurança e tenant
6. atualizar documentação
7. registrar a release
