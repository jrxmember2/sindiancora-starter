# README do Projeto

## Visao geral

SindiAncora e um SaaS multiempresa para gestao condominial com foco em operacao, controle contratual e evolucao segura por fases.

Este documento registra a base formalizada do projeto apos as Fases 0, 1, 2 e 3, organizando:

- arquitetura proposta
- padroes de implementacao
- regras de multiempresa
- estrategia de licenciamento
- estrategia de permissao
- modelo inicial de banco
- deploy no EasyPanel
- backlog executavel das proximas fases

## Ambiente de producao

- URL: `https://sindiancora.serratech.tec.br`
- builder recomendado: `Nixpacks`
- visibilidade de versao: superadmin

## Versao atual da base

- versao: `0.4.0`
- release: `Contract Licensing`
- status: base publicada com login funcional, tenancy endurecido e licenciamento contratual operacional

## O que esta implementado

- autenticacao web com superadmin
- dashboard inicial revisado
- CRUD inicial de empresas
- CRUD inicial de licencas
- catalogo de modulos
- CRUD inicial de condominios
- CRUD inicial de fornecedores
- CRUD inicial de documentos
- CRUD inicial de chamados
- alternancia de empresa na sessao
- middlewares iniciais de licenca e modulo
- tela de versionamento exclusiva do superadmin
- Form Requests nos fluxos principais
- componentes base do painel para tabela, drawer, modal, confirmacao e toast
- validacao da empresa ativa antes do route model binding
- troca de empresa limitada a vinculos ativos
- preparacao de `user_condominiums` e escopo por condominio em chamados/documentos
- historico de licenca em banco
- uso de licenca persistido por empresa
- tela "Minha licenca" para leitura operacional do contrato
- alertas contratuais e status de somente leitura

## O que ainda nao esta endurecido

- policies e gates
- uploads reais de documentos
- worker/scheduler ativos em producao
- auditoria automatica
- notificacoes

## Leitura recomendada

1. [ARQUITETURA.md](ARQUITETURA.md)
2. [BANCO_DE_DADOS.md](BANCO_DE_DADOS.md)
3. [LICENCIAMENTO.md](LICENCIAMENTO.md)
4. [PERMISSOES.md](PERMISSOES.md)
5. [DEPLOY_EASYPANEL.md](DEPLOY_EASYPANEL.md)
6. [ROADMAP.md](ROADMAP.md)
7. [PROMPTS_FASES.md](PROMPTS_FASES.md)
