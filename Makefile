# Beyond Plus CMS — developer setup
# ---------------------------------------------------------------------------
# Requires only: PHP 8.3+, Composer, and MySQL/MariaDB running
# (a bundled stack like XAMPP/Laragon provides all three).
#
# Quick start:   make install   &&   make serve
# Then open:     http://localhost:8899   (admin: /bp-admin, admin@example.com / password)
#
# DB credentials are read from .env (never hard-coded here). Edit .env first if
# your MySQL user/password/host differ from the defaults.
# ---------------------------------------------------------------------------

.DEFAULT_GOAL := help
PORT ?= 8899

## ---- one-click ------------------------------------------------------------

install: vendor env key permissions db ## Full install: deps, .env, app key, permissions, database
	@echo ""
	@echo "  ✔ Beyond Plus CMS is installed."
	@echo "    Start it with:  make serve   →  http://localhost:$(PORT)"
	@echo "    Admin login:    /bp-admin   (admin@example.com / password)"
	@echo ""

## ---- individual steps -----------------------------------------------------

vendor: ## Install PHP dependencies (composer)
	composer install --no-interaction --prefer-dist

env: ## Create .env from .env.example if it does not exist
	@test -f .env || (cp .env.example .env && echo "Created .env from .env.example")

key: ## Generate APP_KEY if it is empty
	@grep -q '^APP_KEY=base64:' .env || php artisan key:generate

permissions: ## Make storage/ and bootstrap/cache writable (775 — not world-writable)
	chmod -R 775 storage bootstrap/cache

db: create-db ## Create the database, then run migrations + seeders
	php artisan migrate --seed --force

db-sample: create-db ## Alternative to `db`: load database/sample-data.sql
	@DB_HOST=$$(grep -E '^DB_HOST=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_PORT=$$(grep -E '^DB_PORT=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_USER=$$(grep -E '^DB_USERNAME=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_PASS=$$(grep -E '^DB_PASSWORD=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_NAME=$$(grep -E '^DB_DATABASE=' .env | cut -d= -f2- | tr -d '"'); \
	 MYSQL_PWD="$$DB_PASS" mysql -h "$${DB_HOST:-127.0.0.1}" -P "$${DB_PORT:-3306}" -u "$${DB_USER:-root}" "$$DB_NAME" < database/sample-data.sql

create-db: ## Create the database if it does not exist (reads .env)
	@DB_HOST=$$(grep -E '^DB_HOST=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_PORT=$$(grep -E '^DB_PORT=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_USER=$$(grep -E '^DB_USERNAME=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_PASS=$$(grep -E '^DB_PASSWORD=' .env | cut -d= -f2- | tr -d '"'); \
	 DB_NAME=$$(grep -E '^DB_DATABASE=' .env | cut -d= -f2- | tr -d '"'); \
	 echo "Creating database $${DB_NAME:-beyondplus_cms} (if needed) ..."; \
	 MYSQL_PWD="$$DB_PASS" mysql -h "$${DB_HOST:-127.0.0.1}" -P "$${DB_PORT:-3306}" -u "$${DB_USER:-root}" \
	   -e "CREATE DATABASE IF NOT EXISTS \`$${DB_NAME:-beyondplus_cms}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

## ---- day to day -----------------------------------------------------------

serve: ## Run the dev server (PORT=8899 by default)
	php artisan serve --port=$(PORT)

test: ## Run the test suite (SQLite in-memory — no DB needed)
	php artisan test

fresh: ## Rebuild the database from scratch (migrate:fresh --seed)
	php artisan migrate:fresh --seed --force

clear: ## Clear config / route / view / cache
	php artisan optimize:clear

## ---- production -----------------------------------------------------------

production: ## Optimised install for a server (no dev deps + caches). Set APP_DEBUG=false first!
	composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	@echo "Reminder: set APP_DEBUG=false and APP_ENV=production in .env, and keep /plugins read-only."

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN{FS = ":.*?## "}{printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

.PHONY: install vendor env key permissions db db-sample create-db serve test fresh clear production help
