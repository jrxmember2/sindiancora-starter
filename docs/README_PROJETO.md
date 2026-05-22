# README do Projeto

## Visão geral

SindiAncora é um SaaS multiempresa para gestão condominial com foco em operação, controle contratual e evolução segura por fases.

Este documento registra a base formalizada do projeto após as Fases 0, 1, 2, 3, 4, 5 e 5.1.

## Ambiente de produção

- URL: `https://sindiancora.serratech.tec.br`
- builder recomendado: `Nixpacks`
- visibilidade de versão: superadmin

## Versão atual da base

- versão: `0.7.0`
- release: `Tenant Governance`
- status: base publicada com separação entre usuários da plataforma e usuários internos das empresas, onboarding comercial completo, primeiro acesso obrigatório e governança de condomínio canônico por documento

## O que está implementado

- superadmin da plataforma com visão macro
- empresas clientes com admin master próprio
- licenciamento contratual por empresa
- módulos liberados por licença
- troca obrigatória de senha no primeiro acesso
- usuários internos com papéis por empresa
- escopo por condomínio para usuários internos
- condomínio canônico com:
  - vínculo principal
  - vínculo solidário
  - solicitação por documento duplicado
  - mescla, transferência e recusa
  - intervenção do superadmin
- chamados e documentos já preparados para respeitar vínculo ativo empresa-condomínio

## O que ainda está pendente

- CRUD de categorias
- endurecimento final de fornecedores
- upload real de documentos
- acompanhamentos de chamados
- relatórios e PDF
- cronograma
- manutenções, obras, pagamentos e orçamentos
- notificações transversais
- integração com WhatsApp
- recursos de IA
- app do condômino

## Leituras recomendadas

1. [ARQUITETURA.md](ARQUITETURA.md)
2. [BANCO_DE_DADOS.md](BANCO_DE_DADOS.md)
3. [LICENCIAMENTO.md](LICENCIAMENTO.md)
4. [PERMISSOES.md](PERMISSOES.md)
5. [DEPLOY_EASYPANEL.md](DEPLOY_EASYPANEL.md)
6. [ROADMAP.md](ROADMAP.md)
7. [PROMPTS_FASES.md](PROMPTS_FASES.md)
