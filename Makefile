# Variables
DOCKER_COMPOSE = docker compose --profile app --profile frontend
EXEC_APP = $(DOCKER_COMPOSE) exec -u www-data app
EXEC_FRONTEND = $(DOCKER_COMPOSE) exec frontend

.PHONY: up down reset sh logs setup migrate test artisan composer npm build dev frontend-install frontend-build frontend-dev

# Archivo de entorno (se crea si no existe)
.env:
	@echo "Configurando archivo .env..."
	@copy .env.example .env 2>nul || cp .env.example .env 2>nul || echo ".env ya existe o error al copiar"

# Comando principal de inicio: Construye, levanta y configura todo
setup: .env
	@echo "Iniciando configuracion de la aplicacion..."
	$(DOCKER_COMPOSE) up -d --build
	@echo "Instalando dependencias de PHP (esto puede tardar la primera vez)..."
	$(EXEC_APP) composer install --no-scripts --no-interaction
	@echo "Generando clave de aplicacion..."
	$(EXEC_APP) php artisan key:generate --ansi
	@echo "Actualizando paquetes de Laravel..."
	$(EXEC_APP) php artisan package:discover --ansi
	@echo "Creando enlaces de almacenamiento (storage)..."
	$(EXEC_APP) php artisan storage:link
	@echo "Ejecutando migraciones de base de datos..."
	$(EXEC_APP) php artisan migrate --force
	@echo "Instalando dependencias del frontend..."
	@$(EXEC_FRONTEND) npm install
	@echo "Aplicacion lista en: https://app.projectegrupb.es"

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down --remove-orphans

reset:
	$(DOCKER_COMPOSE) down -v --remove-orphans
	rm -rf vendor node_modules bootstrap/cache/*.php public/storage
	@echo "✅ Entorno limpiado. Ejecuta 'make setup' para empezar de cero."

sh:
	$(EXEC_APP) bash

logs:
	$(DOCKER_COMPOSE) logs -f --tail=100

migrate:
	$(EXEC_APP) php artisan migrate

test:
	$(EXEC_APP) php artisan test -q

artisan:
	@$(EXEC_APP) php artisan $(CMD)

composer:
	@$(EXEC_APP) composer $(CMD)

npm:
	@$(EXEC_APP) npm $(CMD)

build:
	@$(EXEC_APP) bash -c "npm install && npm run build"

dev:
	@$(EXEC_APP) npm run dev

frontend-install:
	@$(EXEC_FRONTEND) npm install

frontend-build:
	@$(EXEC_FRONTEND) npm run build

frontend-dev:
	@$(EXEC_FRONTEND) npm run dev
