# Prompts das Fases

## Como usar

Use os prompts abaixo como ponto de partida no seu controle de projeto. Eles assumem:

- Fase 0 concluida
- Fase 1 concluida
- Fase 2 concluida
- stack atual: Laravel + PostgreSQL + React + Inertia + Tailwind + EasyPanel

Em qualquer fase, mantenha estas regras:

- nao remover migrations antigas
- nao quebrar rotas existentes sem ajustar o frontend
- nao vazar dados entre empresas
- validar `company_id`, licenca, modulo e permissao
- ao final, rodar testes, build e atualizar docs

## Fase 3 - Licenciamento contratual

```text
Continue o projeto SindiAncora a partir da Fase 2 concluida.

Objetivo da Fase 3:
Completar o nucleo comercial do SaaS com licenciamento contratual personalizado por empresa.

Entregas esperadas:
- criar tabelas e fluxo de license_history e license_usage
- completar o LicenseGuard com verificacoes de storage, IA e WhatsApp
- exibir uso atual x limite contratado
- criar alertas de limite e vencimento
- endurecer status de licenca e status financeiro
- manter tudo isolado por company_id

Regras:
- controllers finos
- services/actions para regra de negocio
- form requests
- nao quebrar CRUDs ja existentes
- atualizar docs e roadmap ao final

Ao finalizar:
1. explique o objetivo
2. liste arquivos alterados
3. implemente
4. rode testes e build
5. atualize documentacao
6. diga se a Fase 3 foi concluida
```

## Fase 4 - Usuarios e permissoes

```text
Continue o projeto SindiAncora a partir da Fase 3 concluida.

Objetivo da Fase 4:
Criar a gestao de usuarios internos da empresa com papeis, permissoes e vinculo por condominio.

Entregas esperadas:
- CRUD de usuarios internos
- papeis: admin da empresa, gestor, operacional e financeiro
- vinculo company_users e user_condominiums com telas
- policies e gates
- limitar acesso por modulo, acao e condominio
- respeitar limite de usuarios internos da licenca

Regras:
- nenhuma consulta operacional por ID puro
- usuario sem permissao nao acessa via menu, URL nem backend
- registrar o suficiente para auditoria futura

Ao finalizar:
1. listar arquivos criados/alterados
2. implementar policies, requests e telas
3. rodar testes de permissao e tenancy
4. atualizar docs
```

## Fase 5 - Condominios

```text
Continue o projeto SindiAncora a partir da Fase 4 concluida.

Objetivo da Fase 5:
Completar o modulo de condominios com dados cadastrais, status, logo e regras de limite por licenca.

Entregas esperadas:
- completar CRUD de condominios
- upload de logo
- filtros e listagem melhores
- limite de condominios ativos
- inativacao sem perda de dados
- logs das alteracoes principais

Regras:
- inativo nao conta no limite
- sempre validar company_id e acesso do usuario
- nao quebrar a base criada na Fase 2
```

## Fase 6 - Categorias e fornecedores

```text
Continue o projeto SindiAncora a partir da Fase 5 concluida.

Objetivo da Fase 6:
Criar os cadastros auxiliares que sustentam os modulos operacionais.

Entregas esperadas:
- CRUD de categorias por tipo
- completar CRUD de fornecedores
- filtros, busca e exportacao simples
- isolamento forte por empresa
- base pronta para uso em chamados, manutencoes e pagamentos

Regras:
- usar form requests e policies
- evitar duplicacao entre categorias e fornecedores
- atualizar docs dos modulos
```

## Fase 7 - Chamados

```text
Continue o projeto SindiAncora a partir da Fase 6 concluida.

Objetivo da Fase 7:
Completar o modulo principal de chamados.

Entregas esperadas:
- detalhes completos do chamado
- status e prioridade conforme roadmap
- responsavel, fornecedor, categoria e prazo
- filtros operacionais e indicadores
- historico basico do chamado
- respeito total a company_id e condominium_id

Regras:
- nao permitir vazamento entre empresas
- preparar base para acompanhamentos na Fase 8
- manter telas responsivas
```

## Fase 8 - Acompanhamentos

```text
Continue o projeto SindiAncora a partir da Fase 7 concluida.

Objetivo da Fase 8:
Criar a timeline operacional do chamado.

Entregas esperadas:
- CRUD de acompanhamentos
- visibilidade interna x publica
- troca de status via acompanhamento
- historico de status e responsavel
- anexos do acompanhamento preparados

Regras:
- acompanhamento interno nao pode vazar para fluxo publico futuro
- manter trilha clara por usuario e data
```

## Fase 9 - Documentos

```text
Continue o projeto SindiAncora a partir da Fase 8 concluida.

Objetivo da Fase 9:
Completar o modulo de documentos com upload real, download seguro e vencimentos.

Entregas esperadas:
- upload real via Laravel Storage
- download respeitando permissao
- status automatico por vigencia
- filtros por tipo, condominio e vencimento
- preparacao para app do condomino e IA

Regras:
- validar tamanho e tipo de arquivo
- respeitar company_id e escopo por condominio
- nao expor caminhos internos de storage
```

## Fase 10 - Dashboard e home operacional

```text
Continue o projeto SindiAncora a partir da Fase 9 concluida.

Objetivo da Fase 10:
Criar uma home operacional com indicadores clicaveis por empresa e condominio.

Entregas esperadas:
- cards de chamados, documentos e prazos
- filtros por periodo e condominio
- links para listagens filtradas
- respeito a permissoes e tenancy

Regras:
- nao calcular indicadores fora do escopo da empresa ativa
- otimizar queries e indices quando necessario
```

## Fase 11 - Relatorios

```text
Continue o projeto SindiAncora a partir da Fase 10 concluida.

Objetivo da Fase 11:
Criar relatorios operacionais e base para PDFs.

Entregas esperadas:
- relatorio de chamados por periodo
- relatorio por condominio
- relatorio com acompanhamentos publicos
- geracao de PDF
- historico de relatorios gerados

Regras:
- nunca incluir acompanhamento interno em relatorio publico
- PDF deve respeitar branding da empresa
```

## Fase 12 - Cronograma

```text
Continue o projeto SindiAncora a partir da Fase 11 concluida.

Objetivo da Fase 12:
Criar a visao em calendario da operacao.

Entregas esperadas:
- calendario mensal
- itens por data
- filtros por condominio
- painel lateral do dia
- navegacao para detalhes

Regras:
- indicadores diarios respeitam permissoes e tenancy
- manter UX clara em desktop e mobile
```

## Fase 13 - Manutencoes

```text
Continue o projeto SindiAncora a partir da Fase 12 concluida.

Objetivo da Fase 13:
Criar o modulo de manutencoes preventivas e corretivas.

Entregas esperadas:
- CRUD de manutencoes
- recorrencia
- anexos
- finalizacao
- integracao com cronograma e dashboard

Regras:
- recorrencia precisa gerar proxima ocorrencia de forma segura
- manter company_id e condominium_id em todas as operacoes
```

## Fase 14 - Obras

```text
Continue o projeto SindiAncora a partir da Fase 13 concluida.

Objetivo da Fase 14:
Criar o acompanhamento de obras e projetos maiores.

Entregas esperadas:
- CRUD de obras
- status, datas e custos
- fornecedor e responsavel
- anexos e acompanhamentos
- integracao com dashboard e cronograma
```

## Fase 15 - Pagamentos

```text
Continue o projeto SindiAncora a partir da Fase 14 concluida.

Objetivo da Fase 15:
Criar o controle de vencimentos, recorrencias e parcelas.

Entregas esperadas:
- CRUD de pagamentos
- tipos simples, recorrente e parcelado
- vencimentos e lembretes
- geracao de parcelas/recorrencias
- notificacoes internas

Regras:
- base preparada para integracao financeira futura
- datas e valores precisam ficar consistentes
```

## Fase 16 - Orcamentos

```text
Continue o projeto SindiAncora a partir da Fase 15 concluida.

Objetivo da Fase 16:
Criar o modulo de orcamentos ligados a chamados, manutencoes e obras.

Entregas esperadas:
- CRUD de orcamentos
- origem do orcamento
- anexos/propostas
- aprovacao e historico
- leitura de pendencias no dashboard
```

## Fase 17 - Preferencias da empresa

```text
Continue o projeto SindiAncora a partir da Fase 16 concluida.

Objetivo da Fase 17:
Criar configuracoes globais da empresa para operacao, app futuro e WhatsApp.

Entregas esperadas:
- preferencias operacionais
- preferencias do app do condomino
- preferencias de WhatsApp e horario
- persistencia em settings por empresa

Regras:
- usar chave/valor estruturado
- documentar chaves novas em BANCO_DE_DADOS e MODULOS
```

## Fase 18 - Auditoria e logs

```text
Continue o projeto SindiAncora a partir da Fase 17 concluida.

Objetivo da Fase 18:
Criar rastreabilidade de alteracoes criticas.

Entregas esperadas:
- audit log automatico
- usuario, empresa, IP e user-agent
- old_values e new_values
- tela de consulta para superadmin
- tela limitada para empresa

Regras:
- nao registrar senha ou dado sensivel indevido
- manter boa legibilidade para suporte e auditoria
```

## Fase 19 - Notificacoes

```text
Continue o projeto SindiAncora a partir da Fase 18 concluida.

Objetivo da Fase 19:
Criar a base de notificacoes internas e por e-mail.

Entregas esperadas:
- inbox interno
- notificacoes por e-mail
- preferencias por usuario
- eventos basicos de chamados, documentos e licenca

Regras:
- usar Notifications do Laravel
- respeitar preferencia do usuario e escopo da empresa
```

## Fase 20 - WhatsApp

```text
Continue o projeto SindiAncora a partir da Fase 19 concluida.

Objetivo da Fase 20:
Preparar a integracao com WhatsApp apos a base operacional estar estavel.

Entregas esperadas:
- configuracao de instancias
- status de conexao
- horario de atendimento
- historico de mensagens
- classificacao de grupos e conversas

Regras:
- modulo precisa respeitar licenca
- limitar instancias conforme contrato
```

## Fase 21 - IA

```text
Continue o projeto SindiAncora a partir da Fase 20 concluida.

Objetivo da Fase 21:
Adicionar recursos inteligentes de apoio operacional.

Entregas esperadas:
- correcao de texto
- resumo de chamado
- sugestao de resposta
- geracao assistida de relatorios
- controle de creditos por licenca

Regras:
- contabilizar consumo por empresa
- tratar dados sensiveis com cuidado
- documentar estrategia antes de integrar provider externo
```

## Fase 22 - App do condomino

```text
Continue o projeto SindiAncora a partir da Fase 21 concluida.

Objetivo da Fase 22:
Preparar backend, API e decisao tecnica para o app mobile do condomino.

Entregas esperadas:
- comparacao curta Flutter x Expo
- recomendacao tecnica para este projeto
- API segura no Laravel
- autenticacao apropriada
- endpoints iniciais de chamados, documentos e avisos

Regras:
- app deve ser nativo, nao webview
- API deve respeitar tenancy, unidade e perfil do morador
```

## Fase 23 - Deploy EasyPanel

```text
Continue o projeto SindiAncora a partir da Fase 22 concluida.

Objetivo da Fase 23:
Fechar o deploy de producao no EasyPanel com worker, scheduler, logs e backup.

Entregas esperadas:
- revisar repositorio para deploy
- PostgreSQL e Redis no projeto EasyPanel
- worker e scheduler separados
- storage link, cache e comandos pos-deploy
- backup e checklist operacional

Regras:
- usar Nixpacks ou Docker de forma consistente
- documentar passo a passo exato da restauracao
```

## Fase 24 - Testes, seguranca e producao

```text
Continue o projeto SindiAncora a partir da Fase 23 concluida.

Objetivo da Fase 24:
Endurecer o sistema para venda com foco em testes, seguranca, performance e observabilidade.

Entregas esperadas:
- testes de autenticacao, tenancy, licenca, permissoes e upload
- revisao de seguranca
- indices e performance
- monitoramento e logs de erro
- checklist final de readiness

Regras:
- nenhuma funcionalidade deve vazar dados entre empresas
- nenhum modulo bloqueado pode ser acessado via URL/API
- deixar claro o que ainda e risco residual
```
