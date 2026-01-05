@echo off
title Instalador CRM Construccion (Laravel 11)
color 0A
echo =====================================================
echo        INSTALADOR AUTOMATICO - CRM CONSTRUCCION
echo =====================================================
echo.
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo Composer no esta en el PATH.
    pause
    exit /b
)
if not exist ".env" (
    echo Creando archivo .env ...
    copy .env.example .env >nul
)
echo Configurando .env ...
powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=crm_construccion_v2' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_USERNAME=.*', 'DB_USERNAME=root' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=http://localhost/crm-construccion-v2/public' | Set-Content .env"
echo Generando APP_KEY...
php artisan key:generate
echo Creando base de datos...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS crm_construccion_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
echo Migrando y sembrando...
php artisan migrate:fresh --seed
echo Limpiando caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo Listo. Usuario: admin@demo.com  Pass: Admin12345
echo Abre: http://localhost/crm-construccion-v2/public/login
pause
