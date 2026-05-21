# Modulos

## Objetivo

Os modulos permitem que cada empresa cliente tenha um produto contractual sob medida, sem liberar tudo para todos.

## Catalogo inicial

- `dashboard`
- `chamados`
- `acompanhamentos`
- `documentos`
- `fornecedores`
- `relatorios`
- `cronograma`
- `manutencoes`
- `obras`
- `pagamentos`
- `orcamentos`
- `whatsapp`
- `ia`
- `app_condomino`
- `reservas`
- `avisos`
- `pets`
- `veiculos`
- `consumo`
- `controle_ferias`
- `configuracoes`

## Estado atual

Ja existem:

- seed do catalogo
- listagem de modulos no superadmin
- sincronizacao de modulos por licenca
- middleware `module:{key}`

## Regras de implementacao

- todo modulo novo deve ter chave estavel
- toda tela operacional deve declarar qual modulo exige
- nenhuma acao de backend pode confiar apenas no menu escondido
- frontend, rota e backend precisam concordar sobre o bloqueio

## Estrategia de rollout

Cada modulo deve ser liberado apenas quando:

- migrations estiverem prontas
- autorizacao estiver implementada
- isolamento multiempresa estiver testado
- build e testes basicos estiverem verdes
