# REST Reference Projects — Coding Guidelines

**Version:** 1.0.0
**Scope:** Stack-agnostic. Applies to every reference project under `be-rest/` (Laravel, Python, Node, .NET, Golang, Rust, Zig, WordPress) and every future AI-generated sample in this repo.
**Status:** Approved for review use.

This guideline is used to review **every** sample and **every** future automated code generation. Reviewers reject on any violation; there are no soft warnings.

---

## 1. Project Layout

Every `be-rest/{stack}/` reference project MUST follow this exact top-level layout:

```
be-rest/{stack}/
├── README.md              # how to run + which env vars
├── .env.example           # every required var, no real secrets
├── .gitignore             # excludes .env, build artifacts, DB files
├── src/                   # application code
│   ├── routes/            # HTTP route registration only
│   ├── controllers/       # request/response glue, no business logic
│   ├── services/          # business logic, framework-agnostic
│   ├── repositories/      # DB access, one file per resource
│   ├── models/            # typed data structures
│   ├── middleware/        # auth, logging, error handling
│   └── config/            # env parsing, DB driver selection
├── migrations/            # SQL or ORM migrations, numbered
├── storage/               # runtime file uploads (gitignored)
└── tests/                 # unit + integration tests
```

Deviations require a stack-specific spec entry under `spec/21-app/0X-{stack}/` explaining the deviation.

---

## 2. Naming Conventions

| Item                       | Rule                                         | Example                        |
|----------------------------|----------------------------------------------|--------------------------------|
| Folders                    | lowercase kebab-case                         | `file-upload/`                 |
| Source files               | stack-idiomatic (snake, kebab, or Pascal)    | `student_repository.py`        |
| Classes / types            | PascalCase                                   | `StudentService`               |
| Functions / methods        | stack-idiomatic (`camelCase` or `snake_case`)| `createStudent`, `create_student` |
| Constants                  | UPPER_SNAKE_CASE                             | `MAX_UPLOAD_BYTES`             |
| Env vars                   | UPPER_SNAKE_CASE, prefixed by domain         | `DB_DRIVER`, `AUTH_JWT_SECRET` |
| HTTP routes                | plural, kebab-case, no trailing slash        | `/students`, `/file-upload`    |
| DB tables                  | plural, snake_case                           | `students`, `uploaded_files`   |
| DB columns                 | snake_case                                   | `created_at`                   |
| JSON keys (API responses)  | camelCase                                    | `createdAt`, `fileId`          |

Booleans must be positively phrased: `isActive`, `hasAccess` — never `isNotActive`, `disabled`.

---

## 3. Function & File Size

- Functions: **≤ 30 lines** of executable code (excluding signature, imports, closing brace).
- Files: **≤ 300 lines**. Split by responsibility, not arbitrary line count.
- Cyclomatic complexity: **≤ 10** per function.
- Nesting depth: **≤ 3** levels. Prefer early return / guard clauses over nested `if`.
- No function does two things — one function, one clear verb in its name.

---

## 4. Language Rules

### 4.1 Forbidden everywhere

- `any` / `dynamic` / `interface{}` / `void*` used to bypass types.
- Silent `catch` blocks (`catch {}`). Every caught error is either logged, rethrown, or converted to a typed error response.
- `TODO` / `FIXME` shipped to `main`. Track work in issues instead.
- Commented-out code.
- Global mutable state outside `config/`.
- Hardcoded secrets, hostnames, ports, file paths, or DB credentials.
- `sleep()` / `Thread.Sleep` in production paths (allowed only in tests with a documented reason).

### 4.2 Required

- Explicit types on public function signatures (return types included).
- All external inputs validated at the controller layer before reaching services.
- All DB access goes through `repositories/`. Controllers and services never write SQL directly.
- All errors surface through the standard JSON envelope from `spec/21-app/00-rest-general/spec.md` §5.

---

## 5. Layering Rules

Strict one-way dependency graph:

```
routes → controllers → services → repositories → DB
                    ↘ middleware ↗
```

- `controllers` may import `services` and `models`. **Never** import repositories.
- `services` may import `repositories` and `models`. **Never** import HTTP types (`Request`, `Response`).
- `repositories` may import `models` and the DB driver only.
- Circular imports are a hard reject.

---

## 6. Configuration

- All configuration read via `src/config/` at startup. No `process.env.X` / `os.getenv` scattered through the codebase.
- Startup fails fast with a clear message when required env vars are missing.
- `.env.example` lists every variable the app reads, grouped by domain (`DB_*`, `AUTH_*`, `STORAGE_*`), with a one-line comment per var.
- Defaults live in `config/`, not sprinkled inline.

---

## 7. Database

- Migrations are numbered and immutable once merged (`0001_create_students.sql`, `0002_...`).
- No destructive migrations without an explicit down/rollback path.
- Every query is parameterized. String concatenation into SQL is a hard reject.
- Repositories return typed models, never raw driver rows.
- The DB driver is selected in `config/` from `DB_DRIVER`; the rest of the code is driver-agnostic.

---

## 8. HTTP & API Contract

- Routes conform to `spec/21-app/00-rest-general/spec.md` §2 exactly (plural, kebab-case, no trailing slash).
- Every response uses the envelope from §5 of that spec. Binary `/file-serve` responses are the only exception.
- HTTP status codes match §5.2 of that spec. No custom codes.
- Error `code` values are UPPER_SNAKE_CASE and drawn from a shared enum per stack (`VALIDATION_ERROR`, `UNAUTHORIZED`, `UNSUPPORTED_MEDIA_TYPE`, `PAYLOAD_TOO_LARGE`, `STORAGE_ERROR`, `INTERNAL_ERROR`).
- Content negotiation: `application/json` in, `application/json` out (except binary serve).

---

## 9. Auth Placeholders

- Auth middleware exists in `src/middleware/` from day one as a pass-through stub that populates `request.principal = null`.
- Handlers read `principal` from middleware — they never parse `Authorization` headers directly.
- When OAuth or JWT is implemented later, only the middleware changes.

---

## 10. Error Handling

- One central error-handling middleware converts thrown/typed errors into the standard envelope.
- Errors carry: `code` (enum), `message` (human), optional `details` (object).
- 5xx responses log the full stack trace server-side; the client body never leaks stack traces, file paths, SQL, or env values.
- Validation errors return `422` with `details` mapping field name → reason.

---

## 11. Logging

- Structured JSON logs to stdout. One line per event.
- Required fields: `ts` (ISO 8601), `level` (`debug|info|warn|error`), `msg`, `requestId`, `route`, `status`, `durationMs`.
- No PII, no tokens, no request bodies containing credentials.
- `console.log` / `print` / `fmt.Println` outside `main`/bootstrap is a hard reject; use the logger.

---

## 12. Testing

- Every endpoint has at least: one success test, one validation-failure test, one auth-failure test.
- File-upload endpoint additionally tests: wrong MIME, oversize, missing field.
- Tests are hermetic — they use SQLite + a temp storage dir, never a shared DB.
- Test names describe behavior: `it_rejects_upload_over_10mb`, not `test1`.
- No network calls in unit tests. Integration tests that need network are tagged and opt-in.

---

## 13. Formatting & Linting

- Each stack MUST wire the community-standard formatter and linter and run both in CI:
  - Laravel → Pint + PHPStan
  - Python → Ruff (format + lint) + Mypy
  - Node → Prettier + ESLint (typescript-eslint) + tsc `--noEmit`
  - .NET → `dotnet format` + Roslyn analyzers
  - Golang → `gofmt` + `go vet` + `staticcheck`
  - Rust → `rustfmt` + `clippy -D warnings`
  - Zig → `zig fmt` + `zig build test`
  - WordPress → PHPCS with WordPress-Extra ruleset
- CI fails on any formatter diff or lint warning. No `// eslint-disable`, `# noqa`, `#[allow(...)]` without a comment explaining why.

---

## 14. Documentation

- `README.md` per stack MUST include: prerequisites, install, env var table, run command, test command, curl examples for every standard endpoint.
- Public functions have a one-line doc comment stating **what** and **why**, not restating the signature.
- Any deviation from this guideline is documented in the stack's spec folder, not in code comments.

---

## 15. Review Checklist (used verbatim by reviewers and code-gen validators)

- [ ] Folder layout matches §1.
- [ ] Naming matches §2 for all files, routes, tables, JSON keys.
- [ ] No forbidden constructs from §4.1.
- [ ] Layering (§5) respected; no circular imports.
- [ ] All env access through `config/` (§6).
- [ ] Migrations numbered; queries parameterized (§7).
- [ ] Response envelope + status codes match spec (§8).
- [ ] Auth middleware present as stub or real (§9).
- [ ] Central error middleware; no leaked internals (§10).
- [ ] Structured logs only (§11).
- [ ] Tests cover success + validation + auth failure for every endpoint (§12).
- [ ] Formatter/linter configured and clean in CI (§13).
- [ ] README complete with curl examples (§14).

Any unchecked box blocks merge.

---

## 16. Cross-References

- General REST spec: `spec/21-app/00-rest-general/spec.md`
- Stack-specific specs: `spec/21-app/01-laravel/` … `spec/21-app/08-wordpress/`
- Reference projects: `be-rest/{stack}/`
- Error conventions: `spec/03-error-manage/`
- DB conventions: `spec/04-database-conventions/`
