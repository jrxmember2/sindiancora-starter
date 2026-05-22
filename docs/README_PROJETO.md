# README do Projeto

## Visão geral

SindiAncora é um SaaS multiempresa para gestão condominial com foco em operação, controle contratual e evolução segura por fases.

Este documento registra a base formalizada do projeto após as Fases 0, 1, 2, 3 e 4, organizando:

- arquitetura proposta
- padrões de implementação
- regras de multiempresa
- estratégia de licenciamento
- estratégia de permissão
- modelo inicial de banco
- deploy no EasyPanel
- backlog executável das próximas fases

## Ambiente de produção

- URL: `https://sindiancora.serratech.tec.br`
- builder recomendado: `Nixpacks`
- visibilidade de versão: superadmin

## Versão atual da base

- versão: `0.5.0`
- release: `User Access Control`
- status: base publicada com login funcional, tenancy endurecido, licenciamento contratual operacional e gestão inicial de usuários internos

## O que está implementado

- autenticação web com superadmin
- dashboard inicial revisado
- CRUD inicial de empresas
- CRUD inicial de licenças
- catálogo de módulos
- CRUD inicial de condomínios
- CRUD inicial de fornecedores
- CRUD inicial de documentos
- CRUD inicial de chamados
- alternância de empresa na sessão
- middlewares iniciais de licença e módulo
- tela de versionamento exclusiva do superadmin
- `Form Requests` nos fluxos principais
- componentes base do painel para tabela, drawer, modal, confirmação e toast
- validação da empresa ativa antes do route model binding
- troca de empresa limitada a vínculos ativos
- preparação de `user_condominiums` e escopo por condomínio em chamados/documentos
- histórico de licença em banco
- uso de licença persistido por empresa
- tela "Minha licença" para leitura operacional do contrato
- alertas contratuais e status de somente leitura
- gestão de usuários internos com papéis por empresa
- policies e gates para a área de usuários
- logs iniciais de criação, edição e inativação de usuários internos

## O que ainda não está endurecido

- uploads reais de documentos
- worker/scheduler ativos em produção
- auditoria transversal completa da plataforma
- notificações
- matriz granular de permissão para todos os módulos operacionais

## Leitura recomendada

1. [ARQUITETURA.md](ARQUITETURA.md)
2. [BANCO_DE_DADOS.md](BANCO_DE_DADOS.md)
3. [LICENCIAMENTO.md](LICENCIAMENTO.md)
4. [PERMISSOES.md](PERMISSOES.md)
5. [DEPLOY_EASYPANEL.md](DEPLOY_EASYPANEL.md)
6. [ROADMAP.md](ROADMAP.md)
7. [PROMPTS_FASES.md](PROMPTS_FASES.md)
