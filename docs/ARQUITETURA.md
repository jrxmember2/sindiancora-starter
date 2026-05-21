# Arquitetura

## Stack

- Laravel
- PostgreSQL
- Redis
- React
- Inertia.js
- TailwindCSS
- Vite

## Princípio central

Tudo que for operacional deve ser filtrado por `company_id`. O sistema usa `SetCurrentCompany` e o trait `BelongsToCompany` para aplicar um escopo global nos modelos operacionais.

## Camadas

- Controllers finos.
- Services para regra de negócio.
- Middlewares para tenant/licença/módulo.
- Models Eloquent.
- React Pages por Inertia.
- Componentes reutilizáveis.
