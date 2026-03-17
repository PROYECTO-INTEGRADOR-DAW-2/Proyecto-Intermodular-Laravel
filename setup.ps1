# setup.ps1 - Comando unificado de inicio para Windows
Write-Host "🚀 Iniciando configuración de la aplicación..." -ForegroundColor Cyan

# 1. Crear .env si no existe
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "✅ Archivo .env creado" -ForegroundColor Green
}

# 2. Levantar contenedores
Write-Host "🐳 Levantando contenedores (Docker Build)..." -ForegroundColor Cyan
docker compose --profile app --profile frontend up -d --build

# 3. Ajustar permisos y Git (Fix para Windows/WSL)
Write-Host "🔧 Ajustando permisos y configuración de Git..." -ForegroundColor Cyan
docker compose --profile app --profile frontend exec -u root app chown -R www-data:www-data /home/app/ftp
docker compose --profile app --profile frontend exec -u root app git config --system --add safe.directory /home/app/ftp

# 4. Instalar dependencias
Write-Host "📦 Instalando dependencias de PHP (esto puede tardar)..." -ForegroundColor Cyan
docker compose --profile app --profile frontend exec -u www-data app composer install --no-scripts --no-interaction

# 5. Configurar Laravel
Write-Host "🔑 Generando clave de aplicación..." -ForegroundColor Cyan
docker compose --profile app --profile frontend exec -u www-data app php artisan key:generate --ansi

Write-Host "📦 Actualizando paquetes de Laravel..." -ForegroundColor Cyan
docker compose --profile app --profile frontend exec -u www-data app php artisan package:discover --ansi

Write-Host "🔗 Creando enlaces de almacenamiento..." -ForegroundColor Cyan
docker compose --profile app --profile frontend exec -u www-data app php artisan storage:link

Write-Host "🗄️ Ejecutando migraciones..." -ForegroundColor Cyan
docker compose --profile app --profile frontend exec -u www-data app php artisan migrate --force

Write-Host "`n✨ ¡Aplicación lista! Accede a https://app.projectegrupb.es" -ForegroundColor Green
Write-Host "Nota: Puedes seguir usando 'make' si instalas GnuWin32 o usas WSL." -ForegroundColor Yellow
