# Prompts das Fases

## Como usar

Use os prompts abaixo como ponto de partida no seu controle de projeto. Eles assumem:

- Fase 0 concluída
- Fase 1 concluída
- Fase 2 concluída
- Fase 3 concluída
- Fase 4 concluída
- stack atual: Laravel + PostgreSQL + React + Inertia + Tailwind + EasyPanel

Em qualquer fase, mantenha estas regras:

- não remover migrations antigas
- não quebrar rotas existentes sem ajustar o frontend
- não vazar dados entre empresas
- validar `company_id`, licença, módulo e permissão
- ao final, rodar testes, build e atualizar docs

## Fase 5 - Condomínios

```text
Continue o projeto SindiAncora a partir da Fase 4 concluída.

Objetivo da Fase 5:
Completar o módulo de condomínios com dados cadastrais, status, logo e regras de limite por licença.

Entregas esperadas:
- completar CRUD de condomínios
- upload de logo
- filtros e listagem melhores
- limite de condomínios ativos
- inativação sem perda de dados
- logs das alterações principais

Regras:
- inativo não conta no limite
- sempre validar company_id e acesso do usuário
- não quebrar a base criada na Fase 2
```

## Fase 6 - Categorias e fornecedores

```text
Continue o projeto SindiAncora a partir da Fase 5 concluída.

Objetivo da Fase 6:
Criar os cadastros auxiliares que sustentam os módulos operacionais.

Entregas esperadas:
- CRUD de categorias por tipo
- completar CRUD de fornecedores
- filtros, busca e exportação simples
- isolamento forte por empresa
- base pronta para uso em chamados, manutenções e pagamentos

Regras:
- usar form requests e policies
- evitar duplicação entre categorias e fornecedores
- atualizar docs dos módulos
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
- respeito total a company_id e condominium_id

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
- anexos do acompanhamento preparados

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
- respeitar company_id e escopo por condomínio
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
- não calcular indicadores fora do escopo da empresa ativa
- otimizar queries e índices quando necessário
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
- finalização
- integração com cronograma e dashboard

Regras:
- recorrência precisa gerar próxima ocorrência de forma segura
- manter company_id e condominium_id em todas as operações
```

## Fase 14 - Obras

```text
Continue o projeto SindiAncora a partir da Fase 13 concluída.

Objetivo da Fase 14:
Criar o acompanhamento de obras e projetos maiores.

Entregas esperadas:
- CRUD de obras
- status, datas e custos
- fornecedor e responsável
- anexos e acompanhamentos
- integração com dashboard e cronograma
```

## Fase 15 - Pagamentos

```text
Continue o projeto SindiAncora a partir da Fase 14 concluída.

Objetivo da Fase 15:
Criar o controle de vencimentos, recorrências e parcelas.

Entregas esperadas:
- CRUD de pagamentos
- tipos simples, recorrente e parcelado
- vencimentos e lembretes
- geração de parcelas/recorrências
- notificações internas

Regras:
- base preparada para integração financeira futura
- datas e valores precisam ficar consistentes
```

## Fase 16 - Orçamentos

```text
Continue o projeto SindiAncora a partir da Fase 15 concluída.

Objetivo da Fase 16:
Criar o módulo de orçamentos ligados a chamados, manutenções e obras.

Entregas esperadas:
- CRUD de orçamentos
- origem do orçamento
- anexos/propostas
- aprovação e histórico
- leitura de pendências no dashboard
```

## Fase 17 - Preferências da empresa

```text
Continue o projeto SindiAncora a partir da Fase 16 concluída.

Objetivo da Fase 17:
Criar configurações globais da empresa para operação, app futuro e WhatsApp.

Entregas esperadas:
- preferências operacionais
- preferências do app do condômino
- preferências de WhatsApp e horário
- persistência em settings por empresa

Regras:
- usar chave/valor estruturado
- documentar chaves novas em BANCO_DE_DADOS e MODULOS
```

## Fase 18 - Auditoria e logs

```text
Continue o projeto SindiAncora a partir da Fase 17 concluída.

Objetivo da Fase 18:
Criar rastreabilidade de alterações críticas.

Entregas esperadas:
- audit log automático
- usuário, empresa, IP e user-agent
- old_values e new_values
- tela de consulta para superadmin
- tela limitada para empresa

Regras:
- não registrar senha ou dado sensível indevido
- manter boa legibilidade para suporte e auditoria
```

## Fase 19 - Notificações

```text
Continue o projeto SindiAncora a partir da Fase 18 concluída.

Objetivo da Fase 19:
Criar a base de notificações internas e por e-mail.

Entregas esperadas:
- inbox interno
- notificações por e-mail
- preferências por usuário
- eventos básicos de chamados, documentos e licença

Regras:
- usar Notifications do Laravel
- respeitar preferência do usuário e escopo da empresa
```

## Fase 20 - WhatsApp

```text
Continue o projeto SindiAncora a partir da Fase 19 concluída.

Objetivo da Fase 20:
Preparar a integração com WhatsApp após a base operacional estar estável.

Entregas esperadas:
- configuração de instâncias
- status de conexão
- horário de atendimento
- histórico de mensagens
- classificação de grupos e conversas

Regras:
- módulo precisa respeitar licença
- limitar instâncias conforme contrato
```

## Fase 21 - IA

```text
Continue o projeto SindiAncora a partir da Fase 20 concluída.

Objetivo da Fase 21:
Adicionar recursos inteligentes de apoio operacional.

Entregas esperadas:
- correção de texto
- resumo de chamado
- sugestão de resposta
- geração assistida de relatórios
- controle de créditos por licença

Regras:
- contabilizar consumo por empresa
- tratar dados sensíveis com cuidado
- documentar estratégia antes de integrar provider externo
```

## Fase 22 - App do condômino

```text
Continue o projeto SindiAncora a partir da Fase 21 concluída.

Objetivo da Fase 22:
Preparar backend, API e decisão técnica para o app mobile do condômino.

Entregas esperadas:
- comparação curta Flutter x Expo
- recomendação técnica para este projeto
- API segura no Laravel
- autenticação apropriada
- endpoints iniciais de chamados, documentos e avisos

Regras:
- app deve ser nativo, não webview
- API deve respeitar tenancy, unidade e perfil do morador
```

## Fase 23 - Deploy EasyPanel

```text
Continue o projeto SindiAncora a partir da Fase 22 concluída.

Objetivo da Fase 23:
Fechar o deploy de produção no EasyPanel com worker, scheduler, logs e backup.

Entregas esperadas:
- revisar repositório para deploy
- PostgreSQL e Redis no projeto EasyPanel
- worker e scheduler separados
- storage link, cache e comandos pós-deploy
- backup e checklist operacional

Regras:
- usar Nixpacks ou Docker de forma consistente
- documentar passo a passo exato da restauração
```

## Fase 24 - Testes, segurança e produção

```text
Continue o projeto SindiAncora a partir da Fase 23 concluída.

Objetivo da Fase 24:
Endurecer o sistema para venda com foco em testes, segurança, performance e observabilidade.

Entregas esperadas:
- testes de autenticação, tenancy, licença, permissões e upload
- revisão de segurança
- índices e performance
- monitoramento e logs de erro
- checklist final de readiness

Regras:
- nenhuma funcionalidade deve vazar dados entre empresas
- nenhum módulo bloqueado pode ser acessado via URL/API
- deixar claro o que ainda é risco residual
```
