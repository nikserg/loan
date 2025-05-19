# Loan Application System

A comprehensive loan application system built with Laravel that demonstrates enterprise-level architectural practices 
including Domain-Driven Design (DDD), Clean Architecture, and SOLID principles.

## Project Overview

This project implements a loan application system with the following core functionality:
- Client management (CRUD operations)
- Loan eligibility assessment based on configurable rules
- Loan application processing with rule-based modifications
- Notification system for application outcomes

## Setup Instructions

1. Clone the repository
2. Start the Docker containers:
   ```
   docker-compose up -d
   ```
3. Install dependencies:
   ```
   docker-compose exec app composer install
   ```
4. Run migrations:
   ```
   docker-compose exec app php artisan migrate
   ```

The API will be available at `http://localhost:8000/api/`

## API Documentation

Documentation is generated using OpenAPI. It is stored in `openapi.yaml`.

### Generate OpenAPI Documentation
```
docker-compose exec app php ./vendor/bin/openapi ./app --output ./openapi.yaml
```

## Testing

Run the test suite:
```
docker-compose exec app php artisan test
```

## Code Quality

Static analysis:
```
./vendor/bin/phpstan analyze --memory-limit 1G
```
