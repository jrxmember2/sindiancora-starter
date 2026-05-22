# Prompts das Fases

## Como usar

Use os prompts abaixo como ponto de partida no seu controle de projeto. Eles assumem:

- Fase 0 concluída
- Fase 1 concluída
- Fase 2 concluída
- Fase 3 concluída
- Fase 4 concluída
- Fase 5 concluída
- Fase 5.1 concluída
- stack atual: Laravel + PostgreSQL + React + Inertia + Tailwind + EasyPanel

Em qualquer fase, mantenha estas regras:

- não remover migrations antigas
- não quebrar rotas existentes sem ajustar o frontend
- não vazar dados entre empresas
- validar empresa ativa, licença, módulo, permissão e vínculo com condomínio
- ao final, rodar testes, build e atualizar docs

## Fase 6 - Categorias e fornecedores

```text
Continue o projeto SindiAncora a partir da Fase 5.1 concluída.

Objetivo da Fase 6:
Criar os cadastros auxiliares que sustentam os módulos operacionais.

Entregas esperadas:
- CRUD de categorias por tipo
- completar CRUD de fornecedores
- filtros, busca e exportação simples
- isolamento forte por empresa
- base pronta para uso em chamados, manutenções e pagamentos

Regras:
- usar Form Requests e Policies
- evitar duplicação entre categorias e fornecedores
- manter acentuação correta na interface
- atualizar documentação dos módulos
```

## Fase 7 - Chamados

```text
Continue o projeto SindiAncora a partir da Fase 6 concluída.

Objetivo da Fase 7:
Completar o módulo principal de chamados.

Entregas esperadas:
- detalhes completos do chamado
- status e prioridade conforme roadmap
- responsável, fornecedor, categoria e prazo
- filtros operacionais e indicadores
- histórico básico do chamado
- respeito total ao vínculo ativo empresa-condomínio

Regras:
- não permitir vazamento entre empresas
- preparar base para acompanhamentos na Fase 8
- manter telas responsivas
```

## Fase 8 - Acompanhamentos

```text
Continue o projeto SindiAncora a partir da Fase 7 concluída.

Objetivo da Fase 8:
Criar a timeline operacional do chamado.

Entregas esperadas:
- CRUD de acompanhamentos
- visibilidade interna x pública
- troca de status via acompanhamento
- histórico de status e responsável
- anexos preparados

Regras:
- acompanhamento interno não pode vazar para fluxo público futuro
- manter trilha clara por usuário e data
```

## Fase 9 - Documentos

```text
Continue o projeto SindiAncora a partir da Fase 8 concluída.

Objetivo da Fase 9:
Completar o módulo de documentos com upload real, download seguro e vencimentos.

Entregas esperadas:
- upload real via Laravel Storage
- download respeitando permissão
- status automático por vigência
- filtros por tipo, condomínio e vencimento
- preparação para app do condômino e IA

Regras:
- validar tamanho e tipo de arquivo
- respeitar empresa ativa e vínculo com condomínio
- não expor caminhos internos de storage
```

## Fase 10 - Dashboard e home operacional

```text
Continue o projeto SindiAncora a partir da Fase 9 concluída.

Objetivo da Fase 10:
Criar uma home operacional com indicadores clicáveis por empresa e condomínio.

Entregas esperadas:
- cards de chamados, documentos e prazos
- filtros por período e condomínio
- links para listagens filtradas
- respeito a permissões e tenancy

Regras:
- não calcular indicadores fora do escopo da empresa ativa e do vínculo com condomínio
- otimizar queries quando necessário
```

## Fase 11 - Relatórios

```text
Continue o projeto SindiAncora a partir da Fase 10 concluída.

Objetivo da Fase 11:
Criar relatórios operacionais e base para PDFs.

Entregas esperadas:
- relatório de chamados por período
- relatório por condomínio
- relatório com acompanhamentos públicos
- geração de PDF
- histórico de relatórios gerados

Regras:
- nunca incluir acompanhamento interno em relatório público
- PDF deve respeitar branding da empresa
```

## Fase 12 - Cronograma

```text
Continue o projeto SindiAncora a partir da Fase 11 concluída.

Objetivo da Fase 12:
Criar a visão em calendário da operação.

Entregas esperadas:
- calendário mensal
- itens por data
- filtros por condomínio
- painel lateral do dia
- navegação para detalhes

Regras:
- indicadores diários respeitam permissões e tenancy
- manter UX clara em desktop e mobile
```

## Fase 13 - Manutenções

```text
Continue o projeto SindiAncora a partir da Fase 12 concluída.

Objetivo da Fase 13:
Criar o módulo de manutenções preventivas e corretivas.

Entregas esperadas:
- CRUD de manutenções
- recorrência
- anexos
- relatório
- presença no cronograma

Regras:
- manter vínculo com condomínio
- preparar integração com fornecedores e categorias
```

## Fase 14 - Obras

```text
Continue o projeto SindiAncora a partir da Fase 13 concluída.

Objetivo da Fase 14:
Criar o módulo de obras.

Entregas esperadas:
- CRUD de obras
- status, orçamento e fornecedor
- acompanhamentos
- anexos
- presença no dashboard e no cronograma
```

## Fase 15 - Pagamentos

```text
Continue o projeto SindiAncora a partir da Fase 14 concluída.

Objetivo da Fase 15:
Criar o módulo de pagamentos com recorrência, parcelas e lembretes.
```

## Fase 16 - Orçamentos

```text
Continue o projeto SindiAncora a partir da Fase 15 concluída.

Objetivo da Fase 16:
Criar o módulo de orçamentos vinculado a chamados, manutenções e obras.
```

## Fase 17 - Preferências da empresa

```text
Continue o projeto SindiAncora a partir da Fase 16 concluída.

Objetivo da Fase 17:
Criar preferências globais por empresa e preparar preferências futuras por condomínio.
```

## Fase 18 - Auditoria e logs

```text
Continue o projeto SindiAncora a partir da Fase 17 concluída.

Objetivo da Fase 18:
Completar a trilha de auditoria da plataforma e das empresas.
```

## Fase 19 - Notificações

```text
Continue o projeto SindiAncora a partir da Fase 18 concluída.

Objetivo da Fase 19:
Criar inbox interno e notificações por e-mail com preferências por usuário.
```

## Fase 20 - WhatsApp

```text
Continue o projeto SindiAncora a partir da Fase 19 concluída.

Objetivo da Fase 20:
Preparar a integração com WhatsApp respeitando licença, horário e governança por empresa.
```

## Fase 21 - IA

```text
Continue o projeto SindiAncora a partir da Fase 20 concluída.

Objetivo da Fase 21:
Adicionar recursos de IA com controle de crédito e consumo por licença.
```

## Fase 22 - App do condômino

```text
Continue o projeto SindiAncora a partir da Fase 21 concluída.

Objetivo da Fase 22:
Preparar API e iniciar o app mobile do condômino.
```

## Fase 23 - Deploy EasyPanel

```text
Continue o projeto SindiAncora a partir da Fase 22 concluída.

Objetivo da Fase 23:
Endurecer o deploy final com worker, scheduler, logs e backup.
```

## Fase 24 - Testes, segurança e produção

```text
Continue o projeto SindiAncora a partir da Fase 23 concluída.

Objetivo da Fase 24:
Fechar os testes críticos, revisão de segurança, performance e readiness comercial.
```
