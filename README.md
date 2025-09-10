# Yii2 Project with Docker

This project is a Yii2 Basic application configured to run in Docker containers, including PHP 8.2, Nginx, and MySQL 8.0.

## 🐳 Docker Configuration

### Prerequisites

- Docker (version 20.10+)
- Docker Compose (version 2.0+)
- Git

### Container Structure

- **app**: PHP 8.2-FPM container with Composer
- **nginx**: Nginx web server (port 8080)
- **db**: MySQL 8.0 database (port 3307)

## 🚀 Installation and Execution

### 1. Clone the project

```bash
git clone <repository-url>
cd exam
```

### 2. Start the containers

```bash
# Build and start all containers
docker-compose up -d --build

# Check if containers are running
docker-compose ps
```

### 3. Install Composer dependencies

```bash
# Enter the application container
docker-compose exec app bash

# Inside the container, install dependencies
composer install

# Exit the container
exit
```
or
```bash
# Run dependency installation outside the container
docker-compose exec app composer install
```

### 4. Configure the database

```bash
# Run migrations (if any)
docker-compose exec app php yii migrate

# Or configure manually in config/db.php file
```

### 5. Access the application

- **Frontend**: http://localhost:8080
- **MySQL**: localhost:3307
  - User: `main`
  - Password: `password`
  - Database: `main`

## 🛠️ Useful Commands

### Container Management

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Stop and remove volumes (CAUTION: deletes database data)
docker-compose down -v

# View logs
docker-compose logs -f

# View logs of a specific service
docker-compose logs -f app
```

### Yii2 Commands

```bash
# Execute Yii commands inside the container
docker-compose exec app php yii

# List available commands
docker-compose exec app php yii help

# Generate cache
docker-compose exec app php yii cache/flush-all
```

### Container Access

```bash
# Enter the application container
docker-compose exec app bash

# Enter the Nginx container
docker-compose exec nginx bash

# Enter the MySQL container
docker-compose exec db mysql -u main -p
```

## 🔧 Configurations

### Database

Database configurations are in `src/config/db.php`:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=db;dbname=main',
    'username' => 'main',
    'password' => 'password',
    'charset' => 'utf8',
];
```

### Nginx

Nginx configuration is in `docker/nginx/default.conf` and points to the `src/web` directory.

### PHP

The PHP Dockerfile is in `docker/php/Dockerfile` with necessary extensions for Yii2.

## 🐛 Troubleshooting

### Container doesn't start

```bash
# Check logs
docker-compose logs

# Rebuild containers
docker-compose down
docker-compose up -d --build
```

### Permission issues

```bash
# Fix permissions in src folder
sudo chown -R $USER:$USER src/
chmod -R 755 src/runtime
chmod -R 755 src/web/assets
```

### Database doesn't connect

- Check if the `db` container is running
- Verify credentials in `src/config/db.php`
- Wait a few seconds for MySQL to initialize completely

## 🧪 Testing

```bash
# Run tests inside the container
docker-compose exec app vendor/bin/codecept run
```

## 📝 Development

For active development:

```bash
# Keep logs visible
docker-compose up

# In another terminal, make code changes
# Changes are automatically reflected via volume mount
```