# Docker Setup for Every Tree for Hope

This project includes a complete Docker configuration for development and production deployment.

## 🐳 Docker Services

### Services Included:

- **app**: Laravel application with PHP 8.2 + Apache
- **mysql**: MySQL 8.0 database
- **redis**: Redis 7 for caching and sessions
- **phpmyadmin**: Database management interface
- **mailhog**: Email testing for development

## 🚀 Quick Start

### Prerequisites:

- Docker Desktop installed and running
- Git

### Setup Steps:

1. **Clone the repository:**

    ```bash
    git clone <repository-url>
    cd every-tree-for-hope
    ```

2. **Copy environment file:**

    ```bash
    cp .dockerenv .env
    ```

3. **Generate application key:**

    ```bash
    docker-compose exec app php artisan key:generate
    ```

4. **Start the containers:**

    ```bash
    # Using the batch script (Windows)
    docker-commands.bat up

    # Or using docker-compose directly
    docker-compose up -d
    ```

5. **Run database migrations:**

    ```bash
    docker-commands.bat migrate
    ```

6. **Seed the database:**
    ```bash
    docker-commands.bat seed
    ```

## 🌐 Access Points

Once running, you can access:

- **Laravel Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
    - Server: mysql
    - Username: root
    - Password: root_password
- **MailHog**: http://localhost:8025 (for testing emails)

## 🛠️ Docker Commands

### Using the Helper Script (Windows):

```bash
# Build images
docker-commands.bat build

# Start containers
docker-commands.bat up

# Stop containers
docker-commands.bat down

# View logs
docker-commands.bat logs

# Access app shell
docker-commands.bat shell

# Run artisan commands
docker-commands.bat artisan list

# Fresh install
docker-commands.bat install

# Clean everything
docker-commands.bat clean
```

### Using Docker Compose Directly:

```bash
# Build and start
docker-compose up -d --build

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Access container
docker-compose exec app bash

# Run artisan
docker-compose exec app php artisan migrate
```

## 📁 Project Structure

```
every-tree-for-hope/
├── Dockerfile                 # Main application container
├── docker-compose.yml         # Service orchestration
├── .dockerenv                 # Docker environment variables
├── .dockerignore             # Files to exclude from build
├── docker-commands.bat       # Windows helper script
├── docker/
│   └── mysql/
│       └── my.cnf            # MySQL configuration
└── ... (rest of Laravel app)
```

## ⚙️ Configuration

### Database Configuration:

- **Host**: mysql
- **Database**: every_tree_for_hope
- **Username**: every_tree_user
- **Password**: secret_password

### Redis Configuration:

- **Host**: redis
- **Port**: 6379

### Mail Configuration (Development):

- **Host**: mailhog
- **Port**: 1025
- **Web Interface**: http://localhost:8025

## 🔧 Development Workflow

### 1. Making Changes:

- Edit files in your local editor
- Changes are automatically synced to the container

### 2. Installing Packages:

```bash
# Composer packages
docker-commands.bat composer require package/name

# NPM packages
docker-compose exec app npm install
```

### 3. Running Commands:

```bash
# Artisan commands
docker-commands.bat artisan migrate

# Composer commands
docker-commands.bat composer install

# Access shell
docker-commands.bat shell
```

### 4. Database Operations:

```bash
# Fresh migration
docker-commands.bat artisan migrate:fresh --seed

# Access MySQL directly
docker-compose exec mysql mysql -u root -p
```

## 🐛 Troubleshooting

### Common Issues:

1. **Port conflicts:**
    - Change ports in `docker-compose.yml` if needed
    - Check what's using the ports: `netstat -an | findstr :8000`

2. **Permission issues:**

    ```bash
    # Fix storage permissions
    docker-compose exec app chown -R www-data:www-data storage
    docker-compose exec app chmod -R 755 storage
    ```

3. **Container won't start:**

    ```bash
    # Check logs
    docker-commands.bat logs

    # Rebuild containers
    docker-commands.bat build
    ```

4. **Database connection issues:**
    - Ensure MySQL container is running
    - Check database credentials in `.env`
    - Verify database exists: `docker-commands.bat artisan db:show`

### Reset Everything:

```bash
docker-commands.bat clean
docker-commands.bat install
```

## 📦 Production Deployment

### Environment Variables:

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
DB_PASSWORD=strong_production_password
```

### Build for Production:

```bash
# Build production image
docker-compose -f docker-compose.yml -f docker-compose.prod.yml build
```

## 🔄 Updating Dependencies

### Update Composer:

```bash
docker-commands.bat composer update
```

### Update NPM:

```bash
docker-compose exec app npm update
```

## 📊 Monitoring

### Container Status:

```bash
docker-compose ps
```

### Resource Usage:

```bash
docker stats
```

### Logs:

```bash
# All containers
docker-commands.bat logs

# Specific service
docker-commands.bat app
docker-commands.bat mysql
```

## 🤝 Contributing

When contributing, ensure:

1. Docker containers build successfully
2. All tests pass in the Docker environment
3. Database migrations work correctly

## 📞 Support

If you encounter issues:

1. Check Docker Desktop is running
2. Verify no port conflicts
3. Check container logs
4. Try rebuilding: `docker-commands.bat build`

---

**Note**: This Docker setup is optimized for development. For production, consider additional security measures and optimizations.
