# Deployment plan

## Recommended first deployment target

For the initial production-oriented MVP, use a managed Laravel hosting environment with:

- a managed PostgreSQL or MySQL database
- a persistent queue worker setup
- scheduled task support
- SSL termination
- object storage for uploads if required

## Environment variables

Set the following values in production:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.example`
- `DB_CONNECTION=mysql` or `pgsql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_MAILER=smtp`
- `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `QUEUE_CONNECTION=database` or a managed queue driver
- `CACHE_STORE=redis` or `database`

## Release checklist

- run `php artisan migrate --force`
- run `php artisan config:cache`
- run `php artisan route:cache`
- run `php artisan view:cache`
- run `npm run build`
- ensure the queue worker is running
- ensure the scheduler is configured in the deployment service
- confirm email transports and notifications are working in a non-production test path

## Operational notes

- keep `.env` out of version control
- avoid exposing admin credentials in the repository or frontend bundle
- use HTTPS and secure cookies in production
- configure log rotation and process monitoring
- verify result approval and standings recalculation in a staging environment before production release

## Future deployment improvements

- zero-downtime or blue/green deploy strategy
- managed Redis or S3-backed storage for notifications and uploads
- load-balancing behind Nginx or another production proxy
- comprehensive monitoring and alerting for queue failures and mail issues
