# PHP Mini Blog

A compact PHP blog with public pages, a password-protected dashboard (posts, tags, categories, comments, users, settings), and a contact form. Data lives in MySQL; outbound mail uses **Mailtrap** (SMTP) when configured, with a fallback to PHP `mail()`.

## Requirements

- **PHP** 8.0 or newer with extensions: `pdo_mysql`, `session`, `json`, `mbstring` (recommended: `intl` for localized dates)
- **Composer** 2.x (for PHPMailer)
- **MySQL** 8.x (local or Docker)

## Quick start

### 1. Clone and install PHP dependencies

```bash
git clone <your-fork-or-repo-url> php-mini-blog
cd php-mini-blog
composer install
```

### 2. Environment

Copy the example env file and edit it:

```bash
cp .env.example .env
```

The app loads `.env` from the project root when present (see `public/index.php`). Values are read with `getenv()`; you can also set the same variables in your web server or shell.

**Database** — defaults match `docker-compose.yml` and `config.php`:

| Variable       | Typical value   |
|----------------|-----------------|
| `DB_HOST`      | `127.0.0.1`     |
| `DB_PORT`      | `3306`          |
| `DB_DATABASE`  | `mini_blog`     |
| `DB_USERNAME`  | `root`          |
| `DB_PASSWORD`  | (see compose)   |

**Contact form (Mailtrap)** — for [Email Testing](https://mailtrap.io), open your sandbox → **Integration** → **SMTP**, then set:

| Variable             | Description                                      |
|----------------------|--------------------------------------------------|
| `MAILTRAP_USER`      | SMTP username from Mailtrap                     |
| `MAILTRAP_PASSWORD`  | SMTP password from Mailtrap                      |
| `MAILTRAP_HOST`      | Default: `sandbox.smtp.mailtrap.io`              |
| `MAILTRAP_PORT`      | Default: `2525`                                  |
| `MAILTRAP_ENCRYPTION`| `tls` (default), `ssl`, or `none`                |
| `CONTACT_EMAIL`      | Address shown as the message recipient (inbox)   |

Optional: `CONTACT_FROM_NAME`, `CONTACT_FROM_EMAIL` for the SMTP `From` header.

If Mailtrap variables are omitted, the contact form still attempts delivery with PHP `mail()` (depends on server configuration).

### 3. Database

Start MySQL (optional, if you use the bundled compose file):

```bash
docker compose up -d
```

Create the schema and apply migrations:

```bash
php database/migrate.php
```

Run this whenever new files appear under `database/migrations/`.

### 4. Run the app

From the **project root**, serve the `public` directory (required so `BASE_PATH` and assets resolve correctly):

```bash
php -S 127.0.0.1:8000 -t public
```

Open **http://127.0.0.1:8000**.

For Apache/Nginx, point the document root at `public/` and route all requests to `public/index.php` (see `public/router.php` for front-controller style setups).

## Features

- **Public**: home, about, contact (POST + validation, honeypot, CSRF, rate limit), blog index and post pages with comments (per site settings).
- **Auth**: login/logout, session-based users with roles: `owner`, `editor`, `author`, `viewer`.
- **Dashboard**: overview, posts (authors limited to their own posts), tags/categories (editors+), comments moderation (editors+), users (owners), site settings (owners), profile (all signed-in users).
- **Email**: contact submissions sent via **PHPMailer** to Mailtrap SMTP when `MAILTRAP_USER` and `MAILTRAP_PASSWORD` are set.

## Project layout

| Path                  | Role |
|-----------------------|------|
| `public/`             | Web root (`index.php`, static assets, upload dirs) |
| `Http/controllers/`   | Route handlers |
| `views/`              | PHP templates |
| `Core/`               | Router, DI container, database, helpers |
| `routes.php`          | HTTP route map |
| `config.php`          | DB config (env-driven) |
| `database/migrations/`| SQL migrations applied by `database/migrate.php` |
| `.env.example`        | Template for local configuration |

## Troubleshooting

- **404 or wrong paths** — Run the built-in server from the **repo root** with `-t public`, not from inside `public/`.
- **Contact mail not arriving** — Confirm Mailtrap credentials under **Email Testing → your sandbox → Integration → SMTP**, and check the Mailtrap inbox. Ensure `composer install` has run so PHPMailer is available.
- **Database errors** — Verify MySQL is up, credentials in `.env` match, and migrations have been applied.

## License

Use and modify freely for your own projects; add a license file if you publish the repo publicly.
