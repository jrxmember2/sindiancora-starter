# Prompt para continuar no Codex

Você está assumindo o projeto **SindiÂncora**, um SaaS multiempresa para gestão condominial.

Antes de alterar qualquer coisa:

1. Leia `README.md`.
2. Leia todos os arquivos da pasta `/docs`.
3. Rode `composer install` e `npm install`.
4. Configure `.env` a partir de `.env.example`.
5. Suba PostgreSQL e Redis com Docker Compose ou pelos serviços do EasyPanel.
6. Rode `php artisan migrate --seed`.
7. Rode `npm run build`.
8. Corrija qualquer erro mínimo de compatibilidade de versão, sem reescrever a arquitetura.

Regras obrigatórias:

- Não quebrar o isolamento por `company_id`.
- Não acessar dados operacionais apenas por ID.
- Toda funcionalidade operacional precisa validar empresa, licença, módulo e permissão.
- Não remover migrations existentes.
- Não apagar dados.
- Trabalhar por issues/fases.
- Atualizar documentação a cada entrega.

Próxima fase sugerida:

## Fase 1 — estabilização do starter

Objetivo:
Validar dependências, instalar o projeto, executar migrations, seeders, build do frontend e corrigir eventuais incompatibilidades de pacote.

Critérios de aceite:

- `composer install` executa sem erro.
- `npm install` executa sem erro.
- `php artisan migrate --seed` executa sem erro.
- Login do superadmin funciona.
- Superadmin consegue criar empresa.
- Superadmin consegue criar licença e liberar módulos.
- Empresa consegue acessar dashboard com licença ativa.
- Módulos bloqueados são impedidos por middleware.
