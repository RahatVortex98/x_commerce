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
- Token-authenticated API for uploading product images from another application.

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
php artisan storage:link
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

To add a local preview catalog with clearly labeled, zero-stock demo items and generated sample artwork, run:

```powershell
php artisan db:seed --class=DemoProductsSeeder
```

The demo catalog seeder is intentionally not called by the default production seeder. Demo items must be replaced with verified product details and real photos before any sale.

## Product image upload API

An external app can upload one image at a time to an existing product's gallery. The endpoint requires a Sanctum bearer token belonging to an administrator with the `product-images:write` ability. Tokens are shown once, expire after 90 days, and are limited to image uploads.

Create a token on the store server:

```powershell
php artisan api:product-image-token admin@x.test --name=supplier-catalog
```

Copy the printed token into the external app's secret manager. Do not put it in source control, browser JavaScript, or a public URL. Revoke that named token if it is lost or no longer needed:

```powershell
php artisan api:product-image-token admin@x.test --name=supplier-catalog --revoke
```

Upload an image using the product's slug (the slug is shown in the product URL):

```powershell
curl.exe -X POST "https://your-store.example/api/products/daily-face-cream/images" `
  -H "Authorization: Bearer YOUR_API_TOKEN" `
  -H "Accept: application/json" `
  -F "image=@C:\images\daily-face-cream.jpg"
```

The multipart form field must be named `image`. JPEG, PNG, and WebP images up to 5 MB are accepted. Each successful request appends an image to the product's `images` media collection and returns its original, thumbnail, and large-image URLs as JSON. Requests are limited to 20 per minute per token. The API does not create products; add the product in Admin first.

The successful response has this shape:

```json
{
  "data": {
    "product": { "id": 12, "slug": "daily-face-cream" },
    "image": {
      "id": 45,
      "file_name": "daily-face-cream.jpg",
      "url": "https://your-store.example/storage/45/daily-face-cream.jpg",
      "thumbnail_url": "https://your-store.example/storage/45/conversions/daily-face-cream-thumb.jpg",
      "large_url": "https://your-store.example/storage/45/conversions/daily-face-cream-large.jpg"
    }
  }
}
```

Authentication failures return `401`; missing token ability or admin role returns `403`; invalid image uploads return `422`; an unknown product slug returns `404`; and rate-limited requests return `429`.

For local public image URLs, run `php artisan storage:link`. In production, configure `MEDIA_DISK` to a public object-storage disk (such as S3) and make sure the disk's URLs are reachable by the external system and storefront.

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
