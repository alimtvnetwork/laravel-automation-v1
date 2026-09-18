# Laravel REST — Reference Sample

Full reference implementation of the general REST spec
(`02-spec/21-app/00-rest-general/spec.md`) using Laravel 11.

## Endpoints

| Method | Path                 | Purpose                          |
|--------|----------------------|----------------------------------|
| GET    | /students            | List all students                |
| GET    | /students/{id}       | Fetch one student                |
| POST   | /students            | Create student                   |
| PUT    | /students/{id}       | Full update                      |
| PATCH  | /students/{id}       | Partial update                   |
| DELETE | /students/{id}       | Delete                           |
| POST   | /file-upload         | Upload allowed file (≤ 10 MB)    |
| GET    | /file-serve/{id}     | Stream stored file inline        |

All JSON responses use the envelope
`{ status, data, message, error }` from `App\Http\Responses\Envelope`.

## Database switching

Set `DB_CONNECTION` in `.env` to `sqlite`, `mysql`, or `pgsql`. No code
changes required — see `config/database.php`.

```
DB_CONNECTION=sqlite   # default
# DB_CONNECTION=mysql
# DB_CONNECTION=pgsql
```

## Bootstrap

```
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

## Tests

```
php artisan test
```

Feature tests cover /students CRUD and /file-upload accept/reject rules
against an in-memory SQLite database (`phpunit.xml`).

## Scope

Auth (OAuth / JWT) is documented in the general spec §6 as a research
hook and is **not** implemented in this reference sample. Endpoint
handlers consume a `principal` from middleware once auth is added.
