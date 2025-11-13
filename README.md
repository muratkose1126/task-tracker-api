<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Task Tracker API

A comprehensive task tracking REST API built with Laravel, featuring project management, task workflows, and role-based access control.

## ✨ Features

- 👥 **User Authentication** - Token-based auth with Laravel Sanctum
- 📋 **Project Management** - Create and manage projects with role-based members
- ✅ **Task Management** - Full CRUD operations with priority levels and status tracking
- 💬 **Task Comments** - Collaborative discussions with activity logging
- 📎 **File Attachments** - Upload files with Spatie Media Library
- 🔐 **Authorization** - Policy-based access control
- � **Activity Tracking** - Spatie Activity Log integration
- ✨ **Code Quality** - Automated checks with Pint & PHPStan
- 🧪 **Test Coverage** - 48+ comprehensive tests

## 🚀 Quick Start

### Requirements

- **PHP** 8.3 or higher
- **MySQL** 8.0 or higher
- **Composer** 2.0 or higher

### Installation

```bash
# Clone the repository
git clone https://github.com/muratkose1126/task-tracker-api.git
cd task-tracker-api

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure your database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=task_tracker
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Optional: Seed sample data
php artisan db:seed
```

### Verification

```bash
# Run tests
php artisan test

# Check code style
./vendor/bin/pint --test

# Run static analysis
./vendor/bin/phpstan analyse
```

## 📖 Available Commands

### Testing & Quality

```bash
php artisan test                    # Run all tests
php artisan test tests/Feature      # Run feature tests
php artisan test tests/Unit         # Run unit tests
php artisan test --coverage         # Generate coverage report

./vendor/bin/pint                   # Auto-fix code style
./vendor/bin/pint --test           # Check code style only
./vendor/bin/phpstan analyse       # Static type analysis
```

### Database

```bash
php artisan migrate                 # Run migrations
php artisan migrate:rollback        # Rollback migrations
php artisan db:seed                # Seed database
php artisan tinker                 # Interactive shell
```

### Development

```bash
php artisan serve                   # Start development server (http://localhost:8000)
php artisan cache:clear            # Clear application cache
php artisan config:clear           # Clear config cache
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/V1/    # API endpoints
│   ├── Requests/V1/           # Form requests & validation
│   ├── Resources/V1/          # API resource responses
├── Models/                     # Eloquent models
├── Policies/                   # Authorization policies
├── Services/V1/                # Business logic layer
├── Enums/                      # Project enums

database/
├── factories/                  # Model factories for testing
├── migrations/                 # Database schema
├── seeders/                    # Database seeders

tests/
├── Feature/                    # Feature tests
├── Unit/                       # Unit tests
```

## 🏗️ Architecture

### Design Patterns

- **Service Layer Pattern** - Business logic separated from controllers
- **FormRequest Validation** - Centralized request validation & authorization
- **Policy Authorization** - Fine-grained access control
- **API Resources** - Consistent and transformable API responses
- **Factory States** - Flexible test data generation

### Database Models

| Model | Purpose |
|-------|---------|
| **User** | User accounts and authentication |
| **Project** | Project containers |
| **ProjectMember** | Project membership with roles |
| **Task** | Task items with status and priority |
| **TaskComment** | Task discussions and interactions |
| **Media** | File attachments |

## 🧪 Testing

```bash
# Run all tests with output
php artisan test

# Run with coverage report
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/Api/V1/TaskTest.php

# Run test with name filter
php artisan test --filter="it_can_create_a_task"

# Run in parallel (faster)
php artisan test --parallel
```

### Test Coverage

- 48+ tests covering features and units
- Feature tests for all API endpoints
- Unit tests for services and models

## 🔄 CI/CD Pipeline

GitHub Actions automatically validates every push and pull request:

- ✅ Unit & Feature Tests
- 🎨 Code Style (Pint)
- 📊 Static Analysis (PHPStan)

View the workflow: [`.github/workflows/tests.yml`](.github/workflows/tests.yml)

## 📝 API Endpoints

### Authentication

```
POST   /api/v1/auth/register      # Register new user
POST   /api/v1/auth/login         # Login
POST   /api/v1/auth/logout        # Logout
POST   /api/v1/auth/me            # Get current user
```

### Projects

```
GET    /api/v1/projects           # List projects
POST   /api/v1/projects           # Create project
GET    /api/v1/projects/{id}      # Get project
PUT    /api/v1/projects/{id}      # Update project
DELETE /api/v1/projects/{id}      # Delete project
```

### Tasks

```
GET    /api/v1/projects/{id}/tasks        # List project tasks
POST   /api/v1/projects/{id}/tasks        # Create task
GET    /api/v1/tasks/{id}                 # Get task
PUT    /api/v1/tasks/{id}                 # Update task
DELETE /api/v1/tasks/{id}                 # Delete task
```

### Task Comments

```
GET    /api/v1/tasks/{id}/comments        # List task comments
POST   /api/v1/tasks/{id}/comments        # Create comment
PUT    /api/v1/task-comments/{id}         # Update comment
DELETE /api/v1/task-comments/{id}         # Delete comment
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests and quality checks:
   ```bash
   php artisan test
   ./vendor/bin/pint
   ./vendor/bin/phpstan analyse
   ```
5. Commit your changes (`git commit -m 'feat: add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Commit Convention

- `feat:` - New feature
- `fix:` - Bug fix
- `refactor:` - Code refactoring
- `test:` - Test additions/modifications
- `docs:` - Documentation changes
- `style:` - Code style fixes (Pint)
- `perf:` - Performance improvements

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

Created by [Murat Köse](https://github.com/muratkose1126)

---

**Useful Links:**
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel API Documentation](https://laravel.com/api)
- [Pest Testing Framework](https://pestphp.com)
- [Spatie Packages](https://spatie.be/open-source)

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
