# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 6 application. Core PHP code lives in `app/`, with HTTP controllers under `app/Http/Controllers`, middleware under `app/Http/Middleware`, queued jobs under `app/Jobs`, exports under `app/Exports`, and service providers under `app/Providers`. Web and API routes are defined in `routes/web.php` and `routes/api.php`. Database migrations, factories, and seeders live in `database/`. Tests are split between `tests/Unit` and `tests/Feature`. Frontend source assets are in `resources/js` and `resources/sass`, compiled into `public/js` and `public/css` through Laravel Mix.

## Build, Test, and Development Commands

- `composer install`: install PHP dependencies.
- `npm install`: install frontend build dependencies.
- `php artisan serve`: run the Laravel development server locally.
- `php artisan migrate`: apply database migrations for the configured environment.
- `php artisan queue:work`: process queued jobs such as `App\Jobs\HelloJob`.
- `vendor/bin/phpunit`: run the PHPUnit test suite.
- `npm run dev`: compile development assets with Laravel Mix.
- `npm run prod`: build optimized production assets.

## Coding Style & Naming Conventions

Follow Laravel and PSR-4 conventions. Use 4-space indentation for PHP, StudlyCase class names such as `UserController` or `UsersExport`, camelCase method names, and descriptive route/controller actions. Keep controllers thin when behavior grows; move reusable logic into jobs, services, exports, or dedicated classes. JavaScript and Sass should stay in `resources/` and be built through `webpack.mix.js` rather than edited directly in `public/`.

## Testing Guidelines

PHPUnit is configured by `phpunit.xml`. Place isolated logic tests in `tests/Unit` and HTTP/application behavior tests in `tests/Feature`. Name test files with the `*Test.php` suffix so PHPUnit discovers them. The test environment uses in-memory SQLite, array cache, array mail, and synchronous queues by default. Run `vendor/bin/phpunit` before submitting changes that affect application behavior.

## Commit & Pull Request Guidelines

Recent commits use short Conventional Commit-style prefixes, for example `feat(user): add export support` and `fix: nginx config`. Prefer `feat(scope): summary`, `fix(scope): summary`, or another clear type when applicable. Pull requests should include a concise description, the reason for the change, affected routes or commands, test results, and screenshots only when UI output changes.

## Security & Configuration Tips

Do not commit real secrets from `.env`. Keep environment-specific values in `.env` and update `.env.example` only with safe defaults. Public web roots should point to `public/`, as shown in `README.md`. Validate authentication and JWT-related changes carefully because `tymon/jwt-auth` is installed even if not all routes currently use it.
