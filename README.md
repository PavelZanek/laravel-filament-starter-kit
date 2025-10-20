# Laravel Filament Starter Kit

This repository is a **Starter Kit** (sometimes referred to as a Boilerplate or Skeleton) for **Laravel 11.x**, **Filament v3**, and several developer-focused tools. Its goal is to provide a clean yet robust foundation for building complex Laravel applications.

> **Note**: This project is still evolving. Use it as a starting point or contribute via pull requests to make it better!

## Overview

- **Laravel 12.x** using [Laravel Sail](https://laravel.com/docs/sail) for local containerized development (but feel free to adapt for other environments such as Herd, etc.).
- **Filament v4** pre-installed with multiple panels (Auth, App, Admin) and a custom language switcher.
- **Livewire v3**, **Alpine.js**, and **Tailwind CSS v4** for interactive frontend components.
- **Database transactions** enabled by default for Filament actions.
- **Database notifications** enabled by default for Filament.
- **SPA (Single Page App)** mode for Filament also enabled by default.
- **Localization** with English and Czech languages pre-configured.
- **Custom Edit Profile** page with a layout similar to Jetstream.

## What's Included

1. **Dev Tools**
    - [Laravel Telescope](https://laravel.com/docs/telescope)
    - [Laravel Horizon](https://laravel.com/docs/horizon)
    - [Debugbar (barryvdh/laravel-debugbar)](https://github.com/barryvdh/laravel-debugbar)
    - [Sentry](https://docs.sentry.io/platforms/php/guides/laravel/)

2. **Testing & Code Quality**
    - [Pest](https://pestphp.com/) (with coverage tests)
    - [Larastan](https://github.com/nunomaduro/larastan) (static analysis)
    - [Rector PHP](https://github.com/rectorphp/rector) (automated refactoring)
    - [Laravel Pint](https://github.com/laravel/pint) (code style & formatting)
    - 100% test coverage + 100% type coverage enforced

3. **Docker / Laravel Sail**
    - Configured via `docker-compose.yml` (included in this repo).
    - Default setup uses **MariaDB** as the database. You can easily switch to MySQL or another database if you prefer—pull requests welcome to improve compatibility!

## Requirements

- **PHP 8.3+**
- **Docker** & **Docker Compose** (if using Laravel Sail)
- [Node.js](https://nodejs.org/) (recommended 22+) & [npm](https://www.npmjs.com/) for frontend assets

## Getting Started

1. **Clone the Repo**
   ```bash
   git clone https://github.com/PavelZanek/laravel-filament-starter-kit.git
   cd laravel-filament-starter-kit
   ```

2. **Configure Environment**
    - Copy `.env.example` to `.env` if not already done automatically.
    - By default, `.env.example` is configured for MariaDB container in Laravel Sail. You can change `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, etc. to match your own setup.
    - Adjust any other settings you need (cache, mail, debug flags, etc.).

3. **Start Laravel Sail**
   ```bash
   ./vendor/bin/sail up -d
   ```
   > On Windows, you may need to run:  
   > `bash vendor/bin/sail up -d`

4. **Install Dependencies**
   ```bash
   sail composer install
   sail npm install
   ```

5. **Build Frontend Assets**
   ```bash
   sail npm run dev
   ```
   or
   ```bash
   sail npm run build
   ```
   for a production build.

6. **Run Migrations & (Optional) Seeds**
   ```bash
   sail artisan migrate
   ```
   You can further seed data or enable additional features as needed.

Your application should be up and running at [http://localhost](http://localhost) (or the URL/port you configured in the `.env`).

## Filament Panels

This Starter Kit comes with **three** Filament panels configured:

1. **Auth Panel**
    - Manages authentication flows (login, password reset, etc.).

2. **App Panel**
    - The main application panel, featuring multi-tenancy via the `Workspace` model.

3. **Admin Panel**
    - Reserved for super-user and administrative functionality.

## Custom Profile Page

A custom **Edit Profile** page is included, styled to resemble Jetstream’s standard layout. This demonstrates how you can override Filament’s default pages to align with your design.

## User Management

The Starter Kit includes a basic user management system to get you started. Under the hood, it uses Filament Shield package to manage roles and permissions.

### User Resource

- Tabs by default roles (+ all users)
- Filters
- Search
- Soft Delete Logic
- Export

### Role Resource

- Default roles (super admin, admin, authenticated)
- One user, one role (but prepared Many-to-Many relation)

## Scripts & Commands

### Running Tests

```bash
sail composer test
```
Runs a full suite that includes:

1. **Rector** (automated refactoring in `--dry-run` mode)
2. **Laravel Pint** (code style checks)
3. **Larastan** (static analysis with PHPStan)
4. **Pest** (with 100% coverage and 100% type coverage checks)

If any step fails, the process will exit with a non-zero code.

Some tests are currently ignored in the test suite. These are primarily related to edge cases or areas requiring further refinement. Contributions are welcome if you'd like to help fine-tune or expand the test coverage and type coverage.

If you're interested in addressing any of these ignored tests, feel free to submit a pull request or open an issue for discussion.

### Other Useful Scripts

- **`sail composer refactor`**: Run Rector in normal (non-dry-run) mode to apply refactoring suggestions.
- **`sail composer lint`**: Run Pint to automatically fix coding style issues.
- **`sail composer test:refactor`**: Run Rector in dry-run mode and exit with a non-zero code if any changes are suggested.
- **`sail composer test:lint`**: Run Pint to check for coding style issues and exit with a non-zero code if any issues are found.
- **`sail composer test:types`**: Run Larastan to check for static analysis issues and exit with a non-zero code if any issues are found.
- **`sail composer test:type-coverage`**: Run Pest with type coverage checks and exit with a non-zero code if the type coverage is below 100%.
- **`sail composer test:unit`**: Run Pest with coverage tests and exit with a non-zero code if the coverage is below 100%.

## Acknowledgments

Special thanks to the following individuals for their invaluable resources and inspiration:

- [Povilas Korop](https://laraveldaily.com/) for his amazing Laravel courses and [Filament Examples](https://filamentexamples.com/).
- [Nuno Maduro](https://www.youtube.com/@nunomaduro/streams) for his insightful livestreams on Laravel development and best practices.

## Contributing

- I develop using **Laravel Sail** locally, but if you want to adapt the project for [Herd](https://herd.dev/) or any other environment, feel free to open a PR.
- If you prefer to use **MySQL**, **PostgreSQL**, or any other database instead of **MariaDB**, you can modify the `.env` and `docker-compose.yml` accordingly. PRs for improved compatibility are welcome!

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).

Happy coding! And feel free to collaborate on making the **Laravel Filament Starter Kit** even better.

---

If you have any further questions or need additional assistance, please don't hesitate to ask. Good luck with your project! 🚀
