# Laravel REST Reference Project

Reserved for Laravel REST automation reference project.

## Status

Scaffolding only. No endpoint logic implemented yet.

## Initialize

This folder holds the target structure. To generate a real Laravel app on top of it:

```bash
composer create-project laravel/laravel . --prefer-dist
```

Then merge the pre-created folders/files (do not overwrite `.env.example`, `config/database.php` customizations, or the `app/` skeleton stubs).

## Structure

```
be-rest/laravel/
├── app/
│   ├── Constants/          # No magic strings — all literals live here
│   ├── Enums/              # Backed enums per spec 18-wp-plugin-how-to/02-enums-and-coding-style
│   ├── Exceptions/         # Typed domain errors per spec 03-error-manage
│   ├── Http/
│   │   ├── Controllers/Api # Thin controllers — delegate to services
│   │   ├── Middleware/
│   │   ├── Requests/       # FormRequest validation
│   │   └── Resources/      # Response envelope shaping
│   ├── Models/             # Eloquent models
│   ├── Providers/
│   ├── Repositories/       # DB access isolation (split-db ready)
│   └── Services/           # Business logic (8–15 line methods)
├── bootstrap/
├── config/
│   ├── app.php
│   ├── database.php        # sqlite | mysql | pgsql switch
│   └── filesystems.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/views/
├── routes/
│   ├── api.php             # /students, /file-upload, /file-serve
│   └── web.php
├── storage/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example
├── artisan
├── composer.json
└── phpunit.xml
```

## Database switching

Per `spec/21-app/00-rest-general/spec.md`, change one env var:

```env
DB_CONNECTION=sqlite   # or mysql, pgsql
```

No code changes required.

## Coding guideline compliance

Follows:
- `spec/02-coding-guidelines/` — style, naming, function size, no negations
- `spec/03-error-manage/` — typed errors, unified envelope, error codes
- `spec/05-split-db-architecture/` — repository layer ready for split
- `spec/01-spec-authoring-guide/` — folder/file naming
