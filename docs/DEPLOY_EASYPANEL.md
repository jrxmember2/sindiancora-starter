# Deploy no EasyPanel

URL de produção:

```txt
https://sindiancora.serratech.tec.br
```

## 1. Criar projeto

No EasyPanel, crie um projeto separado chamado:

```txt
SindiAncora
```

## 2. Criar PostgreSQL

Crie um serviço PostgreSQL no mesmo projeto.

Variáveis que serão usadas no app:

```env
DB_CONNECTION=pgsql
DB_HOST=HOST_DO_POSTGRES
DB_PORT=5432
DB_DATABASE=sindiancora
DB_USERNAME=sindiancora
DB_PASSWORD=SENHA_FORTE
```

## 3. Criar Redis

Crie um serviço Redis no mesmo projeto.

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_CLIENT=phpredis
REDIS_HOST=HOST_DO_REDIS
REDIS_PORT=6379
REDIS_PASSWORD=null
```

## 4. Criar App Laravel

1. Crie novo app.
2. Conecte ao GitHub.
3. Escolha a branch principal.
4. Configure o domínio `sindiancora.serratech.tec.br`.
5. Ative SSL.

## 5. Variáveis de produção

```env
APP_NAME="SindiÂncora"
APP_ENV=production
APP_KEY=base64:GERAR_CHAVE
APP_DEBUG=false
APP_URL=https://sindiancora.serratech.tec.br

SUPERADMIN_NAME="Junior Amorim"
SUPERADMIN_EMAIL="SEU_EMAIL"
SUPERADMIN_PASSWORD="SENHA_FORTE"
```

Gere a chave com:

```bash
php artisan key:generate --show
```

## 6. Comandos pós-deploy

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 7. Queue worker

Crie um processo separado:

```bash
php artisan queue:work redis --sleep=3 --tries=3 --timeout=120
```

## 8. Scheduler

Configure cron a cada minuto:

```bash
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

## 9. Checklist

- [ ] Domínio aponta para a VPS.
- [ ] SSL ativo.
- [ ] APP_KEY configurado.
- [ ] PostgreSQL conectado.
- [ ] Redis conectado.
- [ ] Migrations rodaram.
- [ ] Seed criou o Superadmin.
- [ ] Login funciona.
- [ ] Build do Vite funciona.
- [ ] Queue worker rodando.
- [ ] Backup configurado.
