# My Expenses - Personal Expense Management System

This project was developed with **Yii2 Framework** and is configured to run in Docker containers. I created a complete personal expense management system with JWT authentication and robust test coverage.

## 🚀 Quick Start

### What you'll need

- Docker and Docker Compose (recent version)
- Git to clone the project

### Container Architecture

I structured the project with 3 main containers:
- **app**: PHP 8.2-FPM with all dependencies
- **nginx**: Web server on port 8080
- **db**: MySQL 8.0 on port 3307

## ⚡ Installation in 3 steps

### 1. Clone and enter the project
```bash
git clone https://github.com/csouza1995/my-expenses.git my_expenses
cd my_expenses
```

### 2. Start the containers
```bash
# Build and start everything
docker-compose up -d --build

# Check if everything is running properly
docker-compose ps
```

### 3. Install dependencies
```bash
# Install what's needed
docker-compose exec app composer install

# Access the application
# Frontend: http://localhost:8080
```

Done! The application is already working. The system comes with test data pre-configured.

## 💾 Database Configuration

I configured MySQL to run automatically. If you need to access it:

- **Host**: localhost:3307
- **User**: main
- **Password**: password
- **Database**: main

```bash
# Run migrations if needed - remove parameter --interactive when wants to confirm migration!
docker-compose exec app php yii migrate --interactive=0

# Setup test data for acceptance tests (web interface)
docker-compose exec app php tests/_data/setup_mysql_test_data.php
```

## 🔧 Useful daily commands

```bash
# Container management
docker-compose up -d        # Start
docker-compose down         # Stop
docker-compose logs -f      # View logs in real time

# Access containers
docker-compose exec app bash    # Enter application container
docker-compose exec db mysql -u main -p  # Access MySQL

# Yii2 commands
docker-compose exec app php yii help      # View available commands
docker-compose exec app php yii cache/flush-all  # Clear cache
```

## 🧪 Testing System

One of the parts I'm most proud of in this project is the testing system. I implemented comprehensive coverage using **Codeception** with fully automated setup.

### How to run tests

**⚠️ Important**: Make sure the containers are running before executing tests!

```bash
# First, ensure containers are up and running
docker-compose up -d

# Enter the container
docker-compose exec app bash

# Run all tests (auto-configures on first run!)
vendor/bin/codecept run

# Or by specific category
vendor/bin/codecept run unit        # Unit tests
vendor/bin/codecept run functional  # Functional tests  
vendor/bin/codecept run acceptance  # End-to-end tests
vendor/bin/codecept run api         # API tests

# Another way is run calling a script from outside of container
docker-compose exec app vendor/bin/codecept run
```

### Current coverage status

| Suite | Status | What it tests |
|-------|--------|---------------|
| **Unit** | ✅ 24/24 (100%) | Models, validations, components |
| **Functional** | ✅ 24/24 (100%) | Login flows and forms |
| **Acceptance** | ✅ 16/16 (100%) | Complete user interface |
| **API** | ✅ 27/27 (100%) | REST endpoints and JWT authentication |

**🎉 Total: 90/90 tests (100%) with 511 assertions - Perfect Coverage! 🎉**

### Automatic test data

The system automatically creates:
- **Test user**: tester@example.com / ABCdef123!@#
- **Categories**: Food, Transport, Entertainment, Health, Education
- **Sample expenses** to test filters and pagination

```bash
# If you need to reset the test environment
php tests/_data/reset_test_db.php

# If acceptance tests fail, setup MySQL test data
docker-compose exec app php tests/_data/setup_mysql_test_data.php
```

## 🚨 Common troubleshooting

### Containers won't start

```bash
# Check logs to understand the problem
docker-compose logs

# Rebuild everything from scratch
docker-compose down
docker-compose up -d --build
```

### Acceptance tests failing

If acceptance tests fail (ExpenseCest, LoginCest), it's usually because the test user doesn't exist in MySQL database. Run:

```bash
# Setup test data in MySQL for acceptance tests
docker-compose exec app php tests/_data/setup_mysql_test_data.php

# Then run acceptance tests again
docker-compose exec app vendor/bin/codecept run acceptance
```

### Permission issues

```bash
# Fix permissions (Linux/Mac)
sudo chown -R $USER:$USER src/
chmod -R 755 src/runtime
chmod -R 755 src/web/assets
```

### Can't connect to database

Sometimes MySQL takes a few seconds to initialize completely. Wait a bit and try again. If it persists, check if the `db` container is running with `docker-compose ps`.

## 📁 Project structure

```
exam/
├── docker/              # Docker configurations
│   ├── nginx/          # Nginx config
│   └── php/            # PHP Dockerfile
├── src/                # Yii2 application code
│   ├── config/         # Configurations
│   ├── controllers/    # Application controllers
│   ├── models/         # Models and entities
│   ├── views/          # Templates
│   ├── web/           # Public entry point
│   └── tests/         # Complete test suite
└── docker-compose.yml # Container orchestration
```

## 🎯 Final considerations

This project was developed as part of a **technical assessment** and designed to be a solid foundation for financial management systems. The test coverage is quite comprehensive and the Docker environment makes development and deployment much easier.

The focus was on demonstrating clean code practices, proper testing methodologies, and modern development workflows using containerization.

If you encounter any issues or have suggestions for improvements, feel free to open an issue or send a pull request!

---

**Important note**: On the first test execution, the system automatically configures the SQLite database and necessary data. No manual setup required! 🚀
