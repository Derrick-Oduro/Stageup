# Development setup

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+
- npm
- SQLite for local development, or MySQL/PostgreSQL for a production-like environment

## Local install

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate
php artisan db:seed
php artisan serve
```

## Local development commands

```bash
php artisan test
php artisan serve
npm run dev
php artisan queue:work
php artisan schedule:work
```

## Current project status

This workspace is already configured for local development with SQLite and includes a working Laravel foundation. The `APP_KEY` is already set in `.env`, and the current test suite is passing.

## Recommended workflow

1. work in small, domain-focused changes
2. add tests before or alongside business-rule changes
3. run the relevant test subset after modifying a feature
4. keep migration and model decisions aligned with the chosen tournament engine design

## Notes on configuration

- update `.env` if you move from SQLite to MySQL or PostgreSQL
- configure your email transport for transactional mail before shipping reminders
- consider database caching and queue workers for the later tournament and notification phases
