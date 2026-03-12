@echo off
chcp 65001 >nul
title Mi Bollito - Sistema de Gestión

:: ============================================================
::  MI BOLLITO - Script de inicio y gestión
::  Uso: doble clic o ejecutar desde cmd
:: ============================================================

:MENU
cls
echo.
echo  ╔══════════════════════════════════════════╗
echo  ║        🧁  MI BOLLITO  🧁               ║
echo  ║       Sistema de Gestión v1.0           ║
echo  ╚══════════════════════════════════════════╝
echo.
echo  [1] Iniciar el sistema
echo  [2] Detener el sistema
echo  [3] Reiniciar el sistema
echo  [4] Ver logs en tiempo real
echo  [5] Abrir en el navegador
echo  [6] Estado de los contenedores
echo  [7] Hacer backup de la base de datos
echo  [8] Restaurar backup de la base de datos
echo  [9] Instalar / Primera vez
echo  [0] Salir
echo.
set /p OPCION="  Elige una opción: "

if "%OPCION%"=="1" goto INICIAR
if "%OPCION%"=="2" goto DETENER
if "%OPCION%"=="3" goto REINICIAR
if "%OPCION%"=="4" goto LOGS
if "%OPCION%"=="5" goto ABRIR
if "%OPCION%"=="6" goto ESTADO
if "%OPCION%"=="7" goto BACKUP
if "%OPCION%"=="8" goto RESTAURAR
if "%OPCION%"=="9" goto INSTALAR
if "%OPCION%"=="0" goto SALIR

echo  ⚠️  Opción inválida. Intenta de nuevo.
timeout /t 2 >nul
goto MENU

:: ============================================================
:INICIAR
cls
echo.
echo  🚀 Iniciando Mi Bollito...
echo  ─────────────────────────────────────────
docker-compose up -d
if %ERRORLEVEL% neq 0 (
    echo.
    echo  ❌ Error al iniciar. ¿Está Docker Desktop abierto?
) else (
    echo.
    echo  ✅ Sistema iniciado correctamente.
    echo  🌐 Disponible en: http://localhost:8080
    echo.
    echo  Esperando que la base de datos esté lista...
    timeout /t 5 >nul
    start http://localhost:8080
)
echo.
pause
goto MENU

:: ============================================================
:DETENER
cls
echo.
echo  🛑 Deteniendo Mi Bollito...
echo  ─────────────────────────────────────────
docker-compose down
if %ERRORLEVEL% neq 0 (
    echo  ❌ Error al detener los contenedores.
) else (
    echo  ✅ Sistema detenido correctamente.
)
echo.
pause
goto MENU

:: ============================================================
:REINICIAR
cls
echo.
echo  🔄 Reiniciando Mi Bollito...
echo  ─────────────────────────────────────────
docker-compose down
timeout /t 2 >nul
docker-compose up -d
if %ERRORLEVEL% neq 0 (
    echo  ❌ Error al reiniciar.
) else (
    echo  ✅ Sistema reiniciado correctamente.
    echo  🌐 Disponible en: http://localhost:8080
    timeout /t 4 >nul
    start http://localhost:8080
)
echo.
pause
goto MENU

:: ============================================================
:LOGS
cls
echo.
echo  📋 Logs en tiempo real (Ctrl+C para salir)
echo  ─────────────────────────────────────────
docker-compose logs -f
echo.
pause
goto MENU

:: ============================================================
:ABRIR
cls
echo.
echo  🌐 Abriendo Mi Bollito en el navegador...
echo  ─────────────────────────────────────────
start http://localhost:8080
echo  ✅ Navegador abierto en http://localhost:8080
echo.
pause
goto MENU

:: ============================================================
:ESTADO
cls
echo.
echo  📊 Estado de los contenedores
echo  ─────────────────────────────────────────
docker-compose ps
echo.
echo  💾 Uso de recursos:
docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}" 2>nul
echo.
pause
goto MENU

:: ============================================================
:BACKUP
cls
echo.
echo  💾 Backup de la base de datos
echo  ─────────────────────────────────────────

:: Crear carpeta de backups si no existe
if not exist "backups" mkdir backups

:: Nombre del archivo con fecha y hora
for /f "tokens=1-4 delims=/ " %%a in ('date /t') do set FECHA=%%c%%b%%a
for /f "tokens=1-2 delims=: " %%a in ('time /t') do set HORA=%%a%%b
set HORA=%HORA: =0%
set BACKUP_FILE=backups\backup_%FECHA%_%HORA%.sql

echo  📁 Guardando en: %BACKUP_FILE%
echo.

docker exec mibollito_db pg_dump -U postgres bd_mibollito > %BACKUP_FILE%

if %ERRORLEVEL% neq 0 (
    echo  ❌ Error al crear el backup.
    echo     ¿Está el sistema iniciado?
) else (
    echo  ✅ Backup creado exitosamente: %BACKUP_FILE%
)
echo.
pause
goto MENU

:: ============================================================
:RESTAURAR
cls
echo.
echo  📂 Restaurar backup de la base de datos
echo  ─────────────────────────────────────────

if not exist "backups" (
    echo  ⚠️  No hay backups disponibles.
    echo     Primero crea un backup desde el menú.
    echo.
    pause
    goto MENU
)

echo  Backups disponibles:
echo.
dir /b backups\*.sql 2>nul
echo.
set /p BACKUP_FILE="  Escribe el nombre del archivo (ej: backup_20250101_1200.sql): "

if not exist "backups\%BACKUP_FILE%" (
    echo.
    echo  ❌ Archivo no encontrado: backups\%BACKUP_FILE%
    echo.
    pause
    goto MENU
)

echo.
echo  ⚠️  ADVERTENCIA: Esto reemplazará todos los datos actuales.
set /p CONFIRMAR="  ¿Confirmar restauración? (s/n): "

if /i "%CONFIRMAR%"=="s" (
    echo.
    echo  🔄 Restaurando...
    docker exec -i mibollito_db psql -U postgres bd_mibollito < backups\%BACKUP_FILE%
    if %ERRORLEVEL% neq 0 (
        echo  ❌ Error al restaurar el backup.
    ) else (
        echo  ✅ Base de datos restaurada correctamente.
    )
) else (
    echo  ❌ Restauración cancelada.
)
echo.
pause
goto MENU

:: ============================================================
:INSTALAR
cls
echo.
echo  🔧 Instalación / Primera configuración
echo  ─────────────────────────────────────────
echo.

:: Verificar Docker
docker --version >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo  ❌ Docker no está instalado o no está en el PATH.
    echo.
    echo  Por favor descarga Docker Desktop desde:
    echo  https://www.docker.com/products/docker-desktop
    echo.
    pause
    goto MENU
)

echo  ✅ Docker encontrado.

:: Verificar Docker Compose
docker-compose --version >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo  ❌ docker-compose no está disponible.
    echo     Asegúrate de tener Docker Desktop actualizado.
    echo.
    pause
    goto MENU
)

echo  ✅ Docker Compose encontrado.
echo.
echo  🏗️  Construyendo imágenes (primera vez puede tardar varios minutos)...
echo.
docker-compose build --no-cache
if %ERRORLEVEL% neq 0 (
    echo.
    echo  ❌ Error al construir las imágenes.
    echo.
    pause
    goto MENU
)

echo.
echo  🚀 Iniciando el sistema por primera vez...
docker-compose up -d
if %ERRORLEVEL% neq 0 (
    echo  ❌ Error al iniciar los contenedores.
) else (
    echo.
    echo  ⏳ Esperando que la base de datos se inicialice...
    timeout /t 8 >nul
    echo.
    echo  ╔══════════════════════════════════════════╗
    echo  ║  ✅ ¡Mi Bollito instalado correctamente! ║
    echo  ║                                          ║
    echo  ║  🌐 URL: http://localhost:8080           ║
    echo  ╚══════════════════════════════════════════╝
    echo.
    start http://localhost:8080
)
echo.
pause
goto MENU

:: ============================================================
:SALIR
cls
echo.
echo  👋 ¡Hasta luego!
echo.
timeout /t 2 >nul
exit
