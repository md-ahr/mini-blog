# PHP Mini Blog

A small PHP app with routing, views, and optional MySQL.

## Requirements

- PHP 8.0+
- [Docker](https://docs.docker.com/get-docker/) (optional, for MySQL)

## Quick start

1. **Clone or copy** this project and open a terminal in the project folder.

2. **Environment** — copy `.env` if needed and adjust values. Defaults match `docker-compose.yml` (`mini_blog` database,
   root user).

3. **Database (optional)** — start MySQL:

   ```bash
   docker compose up -d
   ```

   Wait until the container is healthy, then use the app (migrations are not included; add tables when you wire up
   `Core\Database`).

4. **Run the app** — from the **project root**, use the `public` folder as the document root and the built-in router so
   `/` hits `public/index.php`:

   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

5. Open **http://127.0.0.1:8000** in your browser.

## Project layout

| Path                | Role                                                    |
|---------------------|---------------------------------------------------------|
| `public/`           | Web root (`index.php`, `router.php` for the dev server) |
| `Http/controllers/` | Route handlers                                          |
| `views/`            | Templates                                               |
| `Core/`             | Router, container, helpers                              |
| `routes.php`        | Route definitions                                       |

## Notes

- Run `php -S` **from the repo root** with `-t public` so paths and `BASE_PATH` resolve correctly.
- Without Docker, point `.env` at your own MySQL instance or ignore DB until you use it in code.
