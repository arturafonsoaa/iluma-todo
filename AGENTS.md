## Stack

This application is a Monolith Laravel Application using Inertia + Vue 3. Its main ecosystem packages and versions are below. Ensure you abide by these specific packages & versions.

### Backend
- php - 8.5
- laravel/framework (LARAVEL) - v13
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/wayfinder (WAYFINDER) - v0
- laravel/boost (BOOST) - v2
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12

### Frontend
- @inertiajs/vue3 (INERTIA_VUE) - v3
- vue (VUE) - v3
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- tailwindcss (TAILWIND) - v4
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

---

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain.

- `conventional-commits` — Enforces Conventional Commits message format. Activate when creating commits, suggesting commit messages, reviewing commit history conventions, or preparing PR-ready commit series.

---

## Development Environment

- This project uses Docker and Docker Compose for local development.
- The `docker-compose.yml` is located in the root of the project and defines the services needed to run the application.

### Running Commands

- **NEVER** run artisan or npm commands directly on the host machine.
- Always use `docker compose exec iluma <command>` to execute commands inside the application container.

### Examples

- To run artisan commands: `docker compose exec iluma php artisan <command>` instead of `php artisan <command>`.
- To run composer commands: `docker compose exec iluma composer <command>` instead of `composer <command>`.
- To run npm commands: `docker compose exec iluma npm <command>` instead of `npm <command>`.

---

## Laravel Boost (MCP Server)

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

### Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

### URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

### Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

### Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

### Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

---

## Commit Policy (Mandatory)

- **NEVER commit changes without explicit user permission.** Before creating a commit, always ask the user for confirmation unless the user has already explicitly instructed you to commit.
- The only exception is when the user's request itself is the act of committing (e.g., "commit these changes", "commita isso", "crie um commit").
- ALWAYS follow the Conventional Commits format when creating commit messages, and use the `conventional-commits` skill to ensure compliance.
