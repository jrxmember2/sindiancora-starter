# Prompt para continuar no Codex

Voce esta assumindo o projeto **SindiAncora**, um SaaS multiempresa para gestao condominial.

Antes de alterar qualquer coisa:

1. Leia `README.md`.
2. Leia todos os arquivos da pasta `/docs`.
3. Rode `composer install` e `npm install`.
4. Configure `.env` a partir de `.env.example`.
5. Suba PostgreSQL e Redis com Docker Compose ou pelos servicos do EasyPanel.
6. Rode `php artisan migrate --seed`.
7. Rode `npm run build`.
8. Corrija qualquer erro minimo de compatibilidade de versao, sem reescrever a arquitetura.

Regras obrigatorias:

- Nao quebrar o isolamento por `company_id`.
- Nao acessar dados operacionais apenas por ID.
- Toda funcionalidade operacional precisa validar empresa, licenca, modulo e permissao.
- Nao remover migrations existentes.
- Nao apagar dados.
- Trabalhar por issues/fases.
- Atualizar documentacao a cada entrega.

Proxima fase sugerida:

## Fase 1 - estabilizacao da base web

Objetivo:
Consolidar dependencias, validar migrations, fortalecer formularios, componentes base e padroes de UX para o painel.

Criterios de aceite:

- `composer install` executa sem erro.
- `npm install` executa sem erro.
- `php artisan migrate --seed` executa sem erro.
- Login do superadmin funciona.
- Superadmin consegue criar empresa.
- Superadmin consegue criar licenca e liberar modulos.
- Empresa consegue acessar dashboard com licenca ativa.
- Modulos bloqueados sao impedidos por middleware.
