#!/bin/bash

# Docker Commands for Every Tree for Hope Project

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Every Tree for Hope - Docker Commands${NC}"
echo "=================================="

# Function to check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        echo -e "${RED}Docker is not running. Please start Docker first.${NC}"
        exit 1
    fi
}

# Function to show help
show_help() {
    echo "Usage: ./docker-commands.sh [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  build       Build Docker images"
    echo "  up          Start all containers"
    echo "  down        Stop all containers"
    echo "  restart     Restart all containers"
    echo "  logs        Show logs from all containers"
    echo "  app         Show logs from app container"
    echo "  mysql       Show logs from MySQL container"
    echo "  redis       Show logs from Redis container"
    echo "  shell       Access app container shell"
    echo "  artisan     Run Laravel artisan commands"
    echo "  composer    Run Composer commands"
    echo "  migrate     Run database migrations"
    echo "  seed        Run database seeders"
    echo "  install     Fresh install (build, up, migrate, seed)"
    echo "  clean       Remove all containers, images, and volumes"
    echo "  help        Show this help message"
}

# Build Docker images
build() {
    echo -e "${YELLOW}Building Docker images...${NC}"
    docker-compose build --no-cache
    echo -e "${GREEN}Build completed!${NC}"
}

# Start all containers
up() {
    echo -e "${YELLOW}Starting containers...${NC}"
    docker-compose up -d
    echo -e "${GREEN}Containers started!${NC}"
    echo -e "App: http://localhost:8000"
    echo -e "phpMyAdmin: http://localhost:8080"
    echo -e "MailHog: http://localhost:8025"
}

# Stop all containers
down() {
    echo -e "${YELLOW}Stopping containers...${NC}"
    docker-compose down
    echo -e "${GREEN}Containers stopped!${NC}"
}

# Restart all containers
restart() {
    echo -e "${YELLOW}Restarting containers...${NC}"
    docker-compose restart
    echo -e "${GREEN}Containers restarted!${NC}"
}

# Show logs
logs() {
    docker-compose logs -f
}

# Show app logs
app_logs() {
    docker-compose logs -f app
}

# Show MySQL logs
mysql_logs() {
    docker-compose logs -f mysql
}

# Show Redis logs
redis_logs() {
    docker-compose logs -f redis
}

# Access app container shell
shell() {
    echo -e "${YELLOW}Accessing app container shell...${NC}"
    docker-compose exec app bash
}

# Run Laravel artisan commands
artisan() {
    docker-compose exec app php artisan "$@"
}

# Run Composer commands
composer_cmd() {
    docker-compose exec app composer "$@"
}

# Run database migrations
migrate() {
    echo -e "${YELLOW}Running database migrations...${NC}"
    docker-compose exec app php artisan migrate
    echo -e "${GREEN}Migrations completed!${NC}"
}

# Run database seeders
seed() {
    echo -e "${YELLOW}Running database seeders...${NC}"
    docker-compose exec app php artisan db:seed
    echo -e "${GREEN}Seeders completed!${NC}"
}

# Fresh install
install() {
    echo -e "${YELLOW}Starting fresh install...${NC}"
    build
    up
    sleep 10
    migrate
    seed
    echo -e "${GREEN}Fresh install completed!${NC}"
    echo -e "App: http://localhost:8000"
    echo -e "phpMyAdmin: http://localhost:8080"
    echo -e "MailHog: http://localhost:8025"
}

# Clean everything
clean() {
    echo -e "${RED}WARNING: This will remove all containers, images, and volumes!${NC}"
    read -p "Are you sure? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}Cleaning up...${NC}"
        docker-compose down -v --rmi all
        docker system prune -f
        echo -e "${GREEN}Cleanup completed!${NC}"
    else
        echo "Cleanup cancelled."
    fi
}

# Check if Docker is running
check_docker

# Handle commands
case "$1" in
    build)
        build
        ;;
    up)
        up
        ;;
    down)
        down
        ;;
    restart)
        restart
        ;;
    logs)
        logs
        ;;
    app)
        app_logs
        ;;
    mysql)
        mysql_logs
        ;;
    redis)
        redis_logs
        ;;
    shell)
        shell
        ;;
    artisan)
        shift
        artisan "$@"
        ;;
    composer)
        shift
        composer_cmd "$@"
        ;;
    migrate)
        migrate
        ;;
    seed)
        seed
        ;;
    install)
        install
        ;;
    clean)
        clean
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        echo -e "${RED}Unknown command: $1${NC}"
        show_help
        exit 1
        ;;
esac
