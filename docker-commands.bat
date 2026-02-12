@echo off
REM Docker Commands for Every Tree for Hope Project - Windows Version

echo Every Tree for Hope - Docker Commands
echo ==================================

REM Function to check if Docker is running
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo Docker is not running. Please start Docker first.
    pause
    exit /b 1
)

REM Handle commands
if "%1"=="build" goto build
if "%1"=="up" goto up
if "%1"=="down" goto down
if "%1"=="restart" goto restart
if "%1"=="logs" goto logs
if "%1"=="app" goto app_logs
if "%1"=="mysql" goto mysql_logs
if "%1"=="redis" goto redis_logs
if "%1"=="shell" goto shell
if "%1"=="artisan" goto artisan
if "%1"=="composer" goto composer
if "%1"=="migrate" goto migrate
if "%1"=="seed" goto seed
if "%1"=="install" goto install
if "%1"=="clean" goto clean
if "%1"=="help" goto help
if "%1"=="--help" goto help
if "%1"=="-h" goto help

echo Unknown command: %1
goto help

:build
echo Building Docker images...
docker-compose build --no-cache
echo Build completed!
goto end

:up
echo Starting containers...
docker-compose up -d
echo Containers started!
echo App: http://localhost:8000
echo phpMyAdmin: http://localhost:8080
echo MailHog: http://localhost:8025
goto end

:down
echo Stopping containers...
docker-compose down
echo Containers stopped!
goto end

:restart
echo Restarting containers...
docker-compose restart
echo Containers restarted!
goto end

:logs
docker-compose logs -f
goto end

:app_logs
docker-compose logs -f app
goto end

:mysql_logs
docker-compose logs -f mysql
goto end

:redis_logs
docker-compose logs -f redis
goto end

:shell
echo Accessing app container shell...
docker-compose exec app bash
goto end

:artisan
shift
docker-compose exec app php artisan %*
goto end

:composer
shift
docker-compose exec app composer %*
goto end

:migrate
echo Running database migrations...
docker-compose exec app php artisan migrate
echo Migrations completed!
goto end

:seed
echo Running database seeders...
docker-compose exec app php artisan db:seed
echo Seeders completed!
goto end

:install
echo Starting fresh install...
docker-compose build --no-cache
docker-compose up -d
timeout /t 10 >nul
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
echo Fresh install completed!
echo App: http://localhost:8000
echo phpMyAdmin: http://localhost:8080
echo MailHog: http://localhost:8025
goto end

:clean
echo WARNING: This will remove all containers, images, and volumes!
set /p confirm="Are you sure? (y/N): "
if /i "%confirm%"=="y" (
    echo Cleaning up...
    docker-compose down -v --rmi all
    docker system prune -f
    echo Cleanup completed!
) else (
    echo Cleanup cancelled.
)
goto end

:help
echo Usage: docker-commands.bat [COMMAND]
echo.
echo Commands:
echo   build       Build Docker images
echo   up          Start all containers
echo   down        Stop all containers
echo   restart     Restart all containers
echo   logs        Show logs from all containers
echo   app         Show logs from app container
echo   mysql       Show logs from MySQL container
echo   redis       Show logs from Redis container
echo   shell       Access app container shell
echo   artisan     Run Laravel artisan commands
echo   composer    Run Composer commands
echo   migrate     Run database migrations
echo   seed        Run database seeders
echo   install     Fresh install (build, up, migrate, seed)
echo   clean       Remove all containers, images, and volumes
echo   help        Show this help message
goto end

:end
pause
