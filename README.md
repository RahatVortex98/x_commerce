# X Commerce

A Laravel-based beauty and personal-care store for browsing products, shopping by category or concern, and managing store content through an admin area.

## Features

- Product catalogue with categories, brands, product variants, images, and stock.
- Homepage merchandising for:
  - Face, skin, and hair concerns, including Acne, Hyperpigmentation, and Sun Protection.
  - Trending categories, new arrivals, beauty sets, recommendations, discounts, and customer reviews.
- Shopping bag and checkout with delivery details and cash-on-delivery support.
- Admin tools for managing products, categories, brands, banners, concerns, and reviews.
- Role-based admin access.

> Online payment choices may be displayed at checkout, but payment gateways are not configured and do not process payments automatically.

## Requirements

- PHP 8.2 or newer and Composer.
- Node.js and npm.
- SQLite (the default local database) or another database supported by Laravel.

## Local setup (Windows PowerShell)

From the project directory, run:

```powershell
composer install
Copy-Item .env.example .env
if (-not (Test-Path database\database.sqlite)) {
    New-Item -ItemType File database\database.sqlite
}
php artisan key:generate
php artisan migrate --seed
npm ci
npm.cmd run build
php artisan serve --no-reload
```

Open `http://127.0.0.1:8000`. On Windows, `--no-reload` avoids PHP's multi-worker server binding issue when `PHP_CLI_SERVER_WORKERS` is set.

For another database, update the `DB_*` settings in `.env` before running migrations. Never commit `.env`; it contains machine-specific settings and secrets.

## Admin access and content

The local database seeder creates a development administrator:

- Email: `admin@x.test`
- Password: `password`

Sign in at `/login`; administrators are taken to `/admin`. These are development-only credentials. Change the password immediately and configure secure credentials before deploying or exposing the application.

In the admin area:

- Add products and assign categories and relevant concerns.
- Select product flags for new arrivals, combos, and homepage recommendations.
- Mark categories as trending.
- Enter a higher “Was price” to display a discount.
- Add genuine customer reviews, then approve and feature them.
- Add banners and product images to make the storefront your own.

Homepage sections remain visible when empty and show a message until matching products or reviews are added.

## Tests and frontend build

```powershell
php artisan test
npm.cmd run build
```

## Security and deployment

- Do not deploy with the development administrator credentials.
- Set `APP_ENV=production`, `APP_DEBUG=false`, a production `APP_URL`, and a secure `APP_KEY`.
- Use production database credentials and configure mail, storage, backups, and payment processing separately.
- Do not commit `.env`, database files, uploaded media, or generated dependency/build directories.
