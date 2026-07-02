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

The sample data ships a demo administrator and a demo customer:

| Account       | URL                 | Login                        | Password   |
|---------------|---------------------|------------------------------|------------|
| Administrator | `/bp-admin/login`   | `admin@example.com`          | `password` |
| Customer      | `/customer/sign-in` | phone `09000000000`          | `password` |

## Themes

Front-end themes live in `resources/views/theme/<name>/`. The active theme is
stored in the `bp_options` table (`option_name = 'theme'`) and defaults to
`default`. Additional sample themes (`bptheme1`, `bptheme2`) are included.

## API for the mobile SPA

The mobile app is a separate SPA that talks to this CMS over a JSON API.
Interactive docs (OpenAPI / Swagger) are at **`/api/documentation`**, and a
live, self-contained auth-flow example (register / verify / login / profile /
logout) is at **`/spa-example.html`**.

- **Base URL:** `/api/m`
- **Responses:** JSON, enveloped as
  `{ "status": <code>, "data": <payload>, "meta"?: <pagination> }`.
- **Locale:** append `?lang=en` (or `mm`, the default) to content requests.
- **Kill switch:** the whole API returns `503` when *API* is turned off on the
  admin **Configuration** page.

### Public content (read-only)

No auth required; rate limited to **60 requests/min per IP**. List endpoints
accept `?page=` and `?per_page=` (capped at 50).

| Method & path | Description |
|---|---|
| `GET /api/m/home` | Site info, sliders, latest posts, news |
| `GET /api/m/posts` · `GET /api/m/posts/{slug}` | Post list (paginated) · post detail |
| `GET /api/m/pages` · `GET /api/m/pages/{slug}` | Page list · page detail |
| `GET /api/m/menus` | Navigation menu tree |
| `GET /api/m/categories` · `GET /api/m/categories/{slug}/posts` | Categories · posts in a category |
| `GET /api/m/sliders` · `GET /api/m/news` | Sliders · news (paginated) |

### Customer authentication

Token-based. Auth endpoints are rate limited to **5 requests/min per IP** to
resist brute force. A successful login or verification returns a **64-character
token**; send it on protected requests in the **`X-BP-Token`** header. Tokens
are stored hashed server-side and are revoked on logout and password reset.

**1. Register** — required fields follow the registration method configured in
the admin (phone, email, or both). An OTP is sent (written to
`storage/logs/laravel.log` until an SMS/email provider is enabled).

```bash
curl -X POST /api/m/auth/register \
  -d firstname=Aung -d phone=09123456789 \
  -d password=secret123 -d password_confirmation=secret123
# -> { "status":200, "data":{ "message":"...", "identifier":"09123456789" } }
```

**2. Verify the OTP** — returns a token.

```bash
curl -X POST /api/m/auth/verify -d identifier=09123456789 -d code=123456
# -> { "status":200, "data":{ "token":"<64-char>", "customer":{ ... } } }
```

**3. Log in** — phone or email + password.

```bash
curl -X POST /api/m/auth/login -d identifier=09123456789 -d password=secret123
# -> { "status":200, "data":{ "token":"<64-char>", "customer":{ ... } } }
```

**4. Authenticated requests** — send the token in the header.

```bash
curl /api/m/account/profile -H "X-BP-Token: <token>"
curl -X POST /api/m/account/logout -H "X-BP-Token: <token>"   # revokes the token
```

**Password reset** — `POST /api/m/auth/forgot-password` (`identifier`) sends an
OTP; the response never reveals whether the account exists. Then
`POST /api/m/auth/reset-password` (`identifier`, `code`, `password`,
`password_confirmation`) sets the new password and invalidates existing tokens.

| Method & path | Auth | Description |
|---|---|---|
| `POST /api/m/auth/register` | – | Create account, send OTP |
| `POST /api/m/auth/verify` | – | Verify OTP → token |
| `POST /api/m/auth/login` | – | Log in → token |
| `POST /api/m/auth/forgot-password` | – | Send reset OTP |
| `POST /api/m/auth/reset-password` | – | Reset password |
| `GET /api/m/account/profile` | `X-BP-Token` | Current customer |
| `POST /api/m/account/logout` | `X-BP-Token` | Revoke token |

## Configuration notes

- **Locales:** configured in `config/app.php` under `locales` (`en`, `mm`).
  `mm` is served un-prefixed; other locales are served under their prefix
  (e.g. `/en/...`).
- **Google Sheets/Drive export** (optional): set the `GOOGLE_*` variables in
  `.env` and provide `storage/credentials.json` (see
  `storage/credentials.json.example`).
- **Customer OTP / SMS / email:** delivery is configured on the admin
  **Configuration** page (SMSPoh for SMS, Mailgun for email). Until a provider
  is enabled there, the OTP sent during sign-up / password reset is written to
  `storage/logs/laravel.log` instead of delivered.

## Security

Real database dumps and OAuth credentials are intentionally excluded via
`.gitignore`. Never commit `.env`, production SQL dumps, or
`storage/credentials.json`.

## License

Open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
