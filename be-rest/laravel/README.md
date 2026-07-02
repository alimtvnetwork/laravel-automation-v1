# be-rest / laravel

Reserved for Laravel REST automation reference project.

## Status

Scaffold only. No endpoint logic implemented yet — placeholder classes exist so
the folder tree matches the conventions in:

- `spec/01-spec-authoring-guide/`
- `spec/02-coding-guidelines/` (PHP standards under `04-php/`)
- `spec/05-split-db-architecture/`
- `spec/21-app/00-rest-general/spec.md`
- `spec/21-app/00-rest-general/coding-guidelines.md`

## Initialise (later)

When ready to bring in the real framework:

```bash
cd be-rest/laravel
composer create-project laravel/laravel . "^11.0"
php artisan key:generate
```

The placeholder files in `app/`, `bootstrap/`, `public/`, `routes/`,
`config/`, and `tests/` will be replaced by the generated equivalents;
keep `config/database.php`, `.env.example`, and the `Api/` controller
skeletons as the driver-switchable + REST-shape baseline.

## Database driver switching

Configured in `.env` via `DB_CONNECTION`:

| Value    | Backend    |
|----------|------------|
| `sqlite` | SQLite (default, file at `database/database.sqlite`) |
| `mysql`  | MySQL / MariaDB |
| `pgsql`  | PostgreSQL |

See `config/database.php` for connection definitions.

## Folder layout

```
app/
  Enums/
  Exceptions/
  Http/
    Controllers/Api/   # StudentController, FileUploadController, FileServeController
    Middleware/
    Requests/
  Models/              # Student
  Providers/
  Services/
bootstrap/
config/                # app.php, database.php, filesystems.php
database/
  factories/
  migrations/
  seeders/
public/                # index.php front controller
resources/views/
routes/                # api.php, web.php, console.php
storage/
tests/
  Feature/
  Unit/
.env.example
composer.json
phpunit.xml
```
