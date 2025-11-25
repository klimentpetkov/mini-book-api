# MiniBooks API - Laravel 11

A modern, versioned REST API built with **Laravel 11**, supporting **OAuth2 (Passport), role-based access (Spatie Permissions), Redis-backed queues**, and full **OpenAPI documentation**. Designed for real-world production needs with developer tooling such as **Telescope** and **JSON-structured logging**.

## Features
### ✅ RESTful API with JWT (Passport)
### 🔐 Role & Permission system (Spatie)
### 🪝 Redis Queues with retry & backoff
### 📄 Swagger / OpenAPI 3 (L5 Swagger)
### 🧪 Dockerized environment with isolated test database
### 🔍 Telescope for debugging (DEV only)
### 📦 Structured JSON logging with actor/request IDs
### 🧰 Includes full authentication flow, book CRUD, etc.


## Tech Stack
### Laravel 11 (PHP 8.3)
### Passport (OAuth2)
### Spatie/Permission
### MySQL 8
### Redis 7
### Docker / docker-compose
### L5-Swagger
### Laravel Telescope

## Quick Start
### ```# 1. Start all services```
### ```docker compose up -d --build```
### ```# 2. Access the app```
### ```http://localhost:8080```
### ```# 3. API is ready. Run feature tests:```
### ```docker compose exec app vendor/bin/phpunit```

### Automatic installation:
### .env,
### migrations,
### Passport keys,
### seeders,
### Swagger docs - handled on boot.


## Default Credentials
### Email: ```admin@example.com```
### Password: ```password```

## API Docs
### Swagger UI: http://localhost:8080/api/documentation
### OpenAPI JSON: http://localhost:8080/docs

## Postman Setup
### Import the provided collection (mini-book-api.postman_collection.json) and set:
### ```{{base_url}}: http://localhost:8080```
### ```{{token}}: Obtain from /api/v1/login```


## Dev Tools
### Telescope (DEV only):
### ```http://localhost:8080/telescope```

### Swagger Generator (only if you want to run it manually, on boot it is already prepared for you automatically):
### ```docker compose exec app php artisan l5-swagger:generate```

### Common Artisan commands
#### for monitoring
#### ```docker compose exec app php artisan route:list```
#### clearing cache
#### ```docker compose exec app php artisan optimize:clear```
#### internal db and other checks
#### ```docker compose exec app php artisan tinker```

## Docker Services
| Service   | Port | Purpose           |
|-----------|------|-------------------|
|app	    |  --  | Laravel app       |
|nginx	    | 8080 | Public access     |
|mysql	    | 8001 | Main database     |
|redis	    | 6379 | Queues/cache      |
|mysql-test | 8011 | Isolated test DB  |


## Testing
### Runs against an **isolated MySQL test container**.
### ```docker compose exec app vendor/bin/phpunit```
### Tests use ```RefreshDatabase``` and **never touch the main DB**.
### You can run them freely without breaking API state.
