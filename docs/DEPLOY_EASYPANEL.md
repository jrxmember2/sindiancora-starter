# Deploy no EasyPanel

## Ambiente de producao

- dominio: `https://sindiancora.serratech.tec.br`
- app: Laravel + Inertia
- builder recomendado: `Nixpacks`

## Premissas

- repositiorio no GitHub com `composer.lock` e `package-lock.json`
- app publicado em projeto separado no EasyPanel
- DNS apontando para a VPS
- portas `80` e `443` livres

## Estrategia recomendada

Para a base atual, o caminho mais seguro e:

- `App Service` via Nixpacks
- `Postgres` no mesmo projeto
- `Redis` entrar depois que fila/cache/sessao estiverem estabilizados
- `storage` com volume
- `scheduler` desativado ate ajustar `job_batches`

## 1. Criar projeto

Crie um projeto dedicado, por exemplo:

```txt
SindiAncora
```

## 2. Criar Postgres

Crie o servico Postgres no mesmo projeto e anote:

- host
- porta
- database
- usuario
- senha

## 3. Criar o app

- tipo: `App`
- source: GitHub
- branch: producao
- builder: `Nixpacks`

Nao usar o `Dockerfile` atual do repositorio como caminho principal do app web.

## 4. Variaveis de ambiente recomendadas

Para a base atual:

```env
APP_NAME="SindiAncora"
APP_ENV=production
APP_KEY="base64:SUA_CHAVE"
APP_DEBUG=false
APP_URL="https://sindiancora.serratech.tec.br"

APP_VERSION=0.1.0
APP_RELEASE_NAME="Foundation"
APP_RELEASE_STAGE=production
APP_RELEASED_AT=2026-05-21
APP_BUILD_SHA=

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=HOST_DO_POSTGRES
DB_PORT=5432
DB_DATABASE=sindiancora
DB_USERNAME=USUARIO_DO_POSTGRES
DB_PASSWORD="SENHA_FORTE"

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

CACHE_STORE=file
QUEUE_CONNECTION=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="naoresponda@serratech.tec.br"
MAIL_FROM_NAME="SindiAncora"

FILESYSTEM_DISK=local

SUPERADMIN_NAME="Junior Amorim"
SUPERADMIN_EMAIL="SEU_EMAIL"
SUPERADMIN_PASSWORD="SENHA_FORTE"

VITE_APP_NAME="SindiAncora"

NIXPACKS_PHP_ROOT_DIR=/app/public
NIXPACKS_PHP_FALLBACK_PATH=/index.php
```

Observacoes:

- valores com espaco ou caracteres especiais devem ficar entre aspas
- `APP_URL` precisa estar em `https`
- o projeto ja confia em proxy e forca HTTPS em producao

## 5. Volume persistente

Crie um volume para:

```txt
/app/storage
```

## 6. Dominio e SSL

- adicione `sindiancora.serratech.tec.br`
- marque como dominio primario
- habilite SSL

## 7. Primeiro deploy

Depois do build:

```bash
php artisan optimize:clear
php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

Se o banco ainda estiver no primeiro provisionamento e precisar resetar:

```bash
php artisan migrate:fresh --seed --force
```

## 8. Queue worker

Na base atual, nao e obrigatorio ativar worker em producao imediatamente.

Quando a fila estiver operacional, criar servico separado com:

```bash
php artisan queue:work redis --sleep=3 --tries=3 --timeout=120
```

ou, se estiver usando database queue:

```bash
php artisan queue:work database --sleep=3 --tries=3 --timeout=120
```

## 9. Scheduler

Ainda nao ativar cron em producao enquanto o projeto nao criar `job_batches` ou remover a rotina atual.

Depois da correcao:

```bash
* * * * * cd /app && php artisan schedule:run >> /dev/null 2>&1
```

## 10. Logs

Conferir:

- logs do build no EasyPanel
- logs do container
- `storage/logs/laravel.log`

## 11. Backup

Minimo recomendado:

- backup diario do PostgreSQL
- backup de `storage`
- retencao definida
- procedimento de restauracao documentado

## 12. Checklist de producao

- [ ] dominio resolvendo para a VPS
- [ ] SSL ativo
- [ ] build concluido
- [ ] login funcionando
- [ ] superadmin criado
- [ ] migrations aplicadas
- [ ] `storage:link` criado
- [ ] assets do Vite carregando por `https`
- [ ] empresa teste criada
- [ ] licenca teste criada
- [ ] modulo teste liberado
- [ ] condominio teste criado
- [ ] chamado teste criado
- [ ] logs revisados
- [ ] backup definido
