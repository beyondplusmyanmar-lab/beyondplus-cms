# Beyond Plus CMS

A multi-language content-management system built on **Laravel 13** (PHP 8.3+).
It provides an admin panel (`/bp-admin`), locale-prefixed public pages, a
theme system, and a token-authenticated JSON API documented with Swagger.

## Requirements

- PHP **8.3+**
- Composer 2
- MySQL / MariaDB
- Node.js (only if you want to rebuild front-end assets; pre-built assets are committed)

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Create your environment file and app key
cp .env.example .env
php artisan key:generate

# 3. Configure the database in .env
#    DB_DATABASE=beyondplus_cms
#    DB_USERNAME=... DB_PASSWORD=...

# 4. Create the database, then import the sample schema + data
mysql -u root -p -e "CREATE DATABASE beyondplus_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p beyondplus_cms < database/sample-data.sql

# 5. Serve
php artisan serve
```

Alternatively, instead of importing `database/sample-data.sql` you can build the
schema from migrations and seed it:

```bash
php artisan migrate --seed
```

## Demo credentials

The sample data ships a single administrator:

| Field    | Value               |
|----------|---------------------|
| URL      | `/bp-admin/login`   |
| Email    | `admin@example.com` |
| Password | `password`          |

## Themes

Front-end themes live in `resources/views/theme/<name>/`. The active theme is
stored in the `bp_options` table (`option_name = 'theme'`) and defaults to
`default`. Additional sample themes (`bptheme1`, `bptheme2`) are included.

## API documentation

Swagger UI is available at `/api/documentation` once the app is running.
The JSON API authenticates via the `api_token` column (token guard).

## Configuration notes

- **Locales:** configured in `config/app.php` under `locales` (`en`, `mm`).
  `mm` is served un-prefixed; other locales are served under their prefix
  (e.g. `/en/...`).
- **Google Sheets/Drive export** (optional): set the `GOOGLE_*` variables in
  `.env` and provide `storage/credentials.json` (see
  `storage/credentials.json.example`).

## Security

Real database dumps and OAuth credentials are intentionally excluded via
`.gitignore`. Never commit `.env`, production SQL dumps, or
`storage/credentials.json`.

## License

Open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
