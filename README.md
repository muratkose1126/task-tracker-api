<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Task Tracker API

A comprehensive task tracking REST API built with Laravel, featuring project management, task workflows, and real-time collaboration.

## Features

- 👥 User authentication (Sanctum)
- 📋 Project management with roles
- ✅ Task management (CRUD, status, priority)
- 💬 Task comments and activity logging
- 📎 File attachments (Spatie Media Library)
- 🔐 Policy-based authorization
- 📖 OpenAPI/Swagger documentation
- ✨ Code quality (Pint + PHPStan)
- 🧪 Comprehensive test coverage (48+ tests)

## Development Setup

### Prerequisites

- Docker & Docker Compose (recommended)
- PHP 8.3+ (if running locally)
- MySQL 8.0+
- Composer

### Quick Start with Docker

```bash
# Clone and setup
git clone https://github.com/muratkose1126/task-tracker-api.git
cd task-tracker-api

# Build and start containers
docker-compose up -d

# Install dependencies (inside container)
docker exec -it workspace bash
composer install

# Setup database
php artisan migrate

# Generate API documentation
php artisan l5-swagger:generate

# Run tests
php artisan test
```

### Local Development (with Docker containers)

**Important:** All project commands should be run inside Docker for consistency.

```bash
# All commands inside the workspace container
docker exec -it <container-id> bash

# Then run:
composer install
php artisan migrate
php artisan test
./vendor/bin/pint
./vendor/bin/phpstan analyse
```

### Available Commands

```bash
# Testing
php artisan test                    # Run all tests
php artisan test tests/Unit         # Run unit tests only
php artisan test tests/Feature      # Run feature tests only

# Code Quality
./vendor/bin/pint                   # Auto-fix code style
./vendor/bin/pint --test           # Check code style only
./vendor/bin/phpstan analyse       # Static analysis

# Database
php artisan migrate                 # Run migrations
php artisan db:seed                # Seed database
php artisan tinker                 # Interactive shell

# API
php artisan l5-swagger:generate    # Generate API docs
```

## API Documentation

Access the API documentation at:
- **Local**: `http://localhost/api/docs`
- **OpenAPI Spec**: `/storage/api-docs.yaml`

## Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/V1/    # API endpoints
│   ├── Requests/V1/           # Form requests (validation)
│   ├── Resources/V1/          # API resources (responses)
├── Models/                     # Eloquent models
├── Policies/                   # Authorization policies
├── Services/V1/                # Business logic layer
├── Enums/                      # Project enums

database/
├── factories/                  # Model factories
├── migrations/                 # Database schema
├── seeders/                    # Database seeders

tests/
├── Feature/                    # Feature tests
├── Unit/                       # Unit tests
```

## Architecture

### Design Patterns

- **Service Layer**: Business logic separated from controllers
- **FormRequest**: Centralized validation & authorization
- **Policies**: Fine-grained authorization
- **Resources**: Consistent API responses
- **Factory States**: Flexible test data generation

### Database Models

- **User**: User accounts
- **Project**: Project management
- **ProjectMember**: Role-based project membership
- **Task**: Task management
- **TaskComment**: Task discussions
- **Media**: File attachments

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/TaskTest.php

# Run specific test
php artisan test --filter="it can create a new task"
```

## CI/CD

GitHub Actions automatically runs:
- All tests (Feature + Unit)
- Pint code style checks
- PHPStan static analysis

Status badge: [Actions](https://github.com/muratkose1126/task-tracker-api/actions)

## Contributing

1. Create a feature branch: `git checkout -b feature/something`
2. Make your changes
3. Run tests and style checks
4. Commit: `git commit -am 'feat: add something'`
5. Push and create a PR

## License

This project is open-source software licensed under the MIT license.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
