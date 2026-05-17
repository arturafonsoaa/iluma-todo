# Iluma Todo

Iluma Todo is an open-source project management and task tracking application.

The application allows you to create and track tasks, organize work into projects, and keep your daily workflow clear and productive.

## Stack

### Backend
- PHP 8.5 (FPM)
- Laravel 13
- Inertia.js v3
- Laravel Fortify (Authentication)
- MySQL 8.4
- Mailpit

### Frontend
- Vue 3
- Inertia.js v3
- Tailwind CSS v4
- TypeScript
- Vite
- Tiptap (Rich Text Editor)

### Development
- Docker & Docker Compose
- Pest (Testing)
- ESLint & Prettier

## Requirements

- Docker
- Docker Compose

## Getting Started

1. Start the containers with build:

```bash
docker compose up -d --build
```

2. Install PHP dependencies (if needed):

```bash
docker compose exec iluma composer install
```

3. Install Node.js dependencies (if needed):

```bash
docker compose exec iluma npm install
```

4. Copy the environment file (if needed):

```bash
cp .env.example .env
```

5. Generate the application key:

```bash
docker compose exec iluma php artisan key:generate
```

6. Run the migrations:

```bash
docker compose exec iluma php artisan migrate
```

7. Start the frontend development server:

```bash
docker compose exec iluma npm run dev
```

## URLs and Ports

| Service | URL |
|---------|-----|
| Application | http://localhost:8088 |
| Mailpit (UI) | http://localhost:8026 |
| Mailpit (SMTP) | localhost:1026 |
| MySQL (host) | localhost:3307 |

## Common Commands

### Environment

Start the environment:

```bash
docker compose up -d
```

Start with rebuild:

```bash
docker compose up -d --build
```

Stop containers:

```bash
docker compose stop
```

Tear down the environment:

```bash
docker compose down
```

Tear down and remove volumes (warning: deletes database data):

```bash
docker compose down -v
```

Check status:

```bash
docker compose ps
```

View logs:

```bash
docker compose logs -f
```

### Laravel / PHP

Run Artisan:

```bash
docker compose exec iluma php artisan <command>
```

Run migrations:

```bash
docker compose exec iluma php artisan migrate
```

Rollback migration:

```bash
docker compose exec iluma php artisan migrate:rollback
```

Clear cache:

```bash
docker compose exec iluma php artisan optimize:clear
```

Access PHP container shell:

```bash
docker compose exec iluma sh
```

### Composer

Install dependencies:

```bash
docker compose exec iluma composer install
```

Update dependencies:

```bash
docker compose exec iluma composer update
```

### Frontend (npm)

Start development server:

```bash
docker compose exec iluma npm run dev
```

Build for production:

```bash
docker compose exec iluma npm run build
```

Build with SSR:

```bash
docker compose exec iluma npm run build:ssr
```

Check code formatting:

```bash
docker compose exec iluma npm run format:check
```

Format code:

```bash
docker compose exec iluma npm run format
```

Run linter:

```bash
docker compose exec iluma npm run lint
```

### Tests

Run tests:

```bash
docker compose exec iluma php artisan test --compact
```

## Database

Configuration used in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=iluma
DB_USERNAME=iluma
DB_PASSWORD=iluma
```

> **Note:** Inside Docker, the database host is `mysql` (service name), not `localhost`.

## E-mails (Mailpit)

Configuration used in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

The inbox can be accessed at `http://localhost:8026`.

## Quick Troubleshooting

Permission errors on `storage` or `bootstrap/cache`:

```bash
docker compose exec iluma sh -lc 'chmod -R ug+rw storage bootstrap/cache'
```

Application not reflecting changes:

- Check if containers are running: `docker compose ps`
- Rebuild: `docker compose up -d --build`
- Clear Laravel cache: `docker compose exec iluma php artisan optimize:clear`

Database connection issues:

- Confirm `.env` has `DB_HOST=mysql`
- Restart services: `docker compose restart mysql iluma`
