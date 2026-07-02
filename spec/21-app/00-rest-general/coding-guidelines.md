# REST Sample — Universal Coding Guidelines

**Version:** 1.0.0
**Updated:** 2026-07-02
**Scope:** Applies to every stack under `be-rest/` (Laravel, Python, Node, .NET, Golang, Rust, Zig, WordPress). Stack-agnostic. Used to review every sample and every future automated code generation.

---

## 1. Folder & File Naming Conventions

### 1.1 Spec files (under `spec/`)
Follow `spec/01-spec-authoring-guide/02-naming-conventions.md` verbatim:
- Folders: `{NN}-{kebab-case}/` (two-digit zero-padded prefix).
- Files: `{NN}-{kebab-case}.md`.
- Reserved prefixes: `00-overview.md`, `97-acceptance-criteria.md`, `98-changelog.md`, `99-consistency-report.md`.
- Every `.md` starts with H1 + `**Version:**` + `**Updated:** YYYY-MM-DD` + `---`.

### 1.2 Source code files (under `be-rest/{stack}/`)
No numeric prefixes. Use the stack's idiomatic casing:

| Artifact | Convention | Example |
|---|---|---|
| Folders | kebab-case | `student-service/`, `file-upload/` |
| Class/type files | Match the exported type's casing | `StudentController.php`, `student_controller.py`, `studentController.ts`, `StudentController.cs`, `student_controller.go`, `student_controller.rs` |
| Config files | kebab-case or stack default | `app-config.yaml`, `appsettings.json`, `.env` |
| Test files | Mirror source + stack suffix | `student_controller_test.go`, `StudentController.test.ts`, `test_student_controller.py`, `StudentControllerTests.cs` |

### 1.3 Endpoint folder layout (all stacks)
```
be-rest/{stack}/
├── src/                (or app/, cmd/, project root per stack)
│   ├── controllers/    # HTTP handlers
│   ├── services/       # business logic
│   ├── repositories/   # DB access
│   ├── models/         # entities / DTOs
│   ├── middleware/     # auth, logging, error handler
│   ├── config/         # env + DB driver selection
│   └── routes/         # route registration
├── tests/
├── migrations/
├── .env.example
└── README.md
```
Stacks may rename the leaf folder to the idiom (`internal/` for Go, `Controllers/` for .NET), but the seven logical groupings above MUST be identifiable.

---

## 2. Code Style Rules

- **Formatter is mandatory.** Every sample MUST format with the stack's canonical tool before commit: `php-cs-fixer` / `black` + `ruff format` / `prettier` / `dotnet format` / `gofmt` + `goimports` / `rustfmt` / `zig fmt`.
- **Linter is mandatory.** `phpstan`, `ruff`/`mypy`, `eslint`, Roslyn analyzers, `golangci-lint`, `clippy`, `zig build test`.
- **Indentation:** stack default (4 for PHP/Python/C#, 2 for JS/TS/JSON/YAML, tab for Go, 4 for Rust/Zig).
- **Line length:** ≤ 120 chars.
- **Encoding:** UTF-8, LF line endings, trailing newline at EOF, no BOM.
- **No dead code, no commented-out blocks, no `TODO` without an issue reference.**
- **Imports:** grouped stdlib → third-party → local, blank line between groups.

---

## 3. Naming Conventions (classes, functions, variables)

Use the stack's native casing — do NOT force one casing across languages:

| Symbol | PHP / C# / Rust type / Zig type | Python / Go var | JS/TS | Go type | Rust fn/var |
|---|---|---|---|---|---|
| Class / Type | `PascalCase` | `PascalCase` | `PascalCase` | `PascalCase` | `PascalCase` |
| Function / Method | `camelCase` (PHP/JS/TS), `PascalCase` (C#/Go exported) | `snake_case` | `camelCase` | `PascalCase`/`camelCase` | `snake_case` |
| Variable | `camelCase` / `snake_case` per stack | `snake_case` | `camelCase` | `camelCase` | `snake_case` |
| Constant | `UPPER_SNAKE_CASE` | `UPPER_SNAKE_CASE` | `UPPER_SNAKE_CASE` | `PascalCase` (exported const) | `UPPER_SNAKE_CASE` |
| File-private | leading `_` where idiomatic | leading `_` | `#private` or `_` | lowercase first letter | default |

Universal rules:
- **Nouns for types**, **verbs for functions** (`StudentService`, `createStudent`).
- **Boolean names read as questions:** `isActive`, `hasParent`, `canEdit`.
- **No abbreviations** except industry-standard (`id`, `url`, `db`, `jwt`).
- **Plural for collections** (`students`, not `studentList`).
- **Repository methods:** `findById`, `findAll`, `create`, `update`, `deleteById`.
- **Service methods:** business verbs (`enrollStudent`, `promoteStudent`).
- **Controller methods:** match HTTP verb intent (`index`, `show`, `store`, `update`, `destroy`) or (`list`, `get`, `create`, `update`, `delete`).

---

## 4. Error Handling Pattern

Every stack MUST implement the same three-layer rule (mirrors `spec/03-error-manage/`):

1. **Catch → log → rethrow or handle.** Silent catch is forbidden.
2. **Wrap at layer boundaries.** Repository errors wrap the driver error; service wraps repository; controller translates to HTTP.
3. **Never stringify at the boundary** — pass the raw error object to the logger; the logger serializes.

### 4.1 Error taxonomy (map to HTTP)
| Class | HTTP | When |
|---|---|---|
| `ValidationError` | 400 / 422 | bad input shape or business rule |
| `AuthenticationError` | 401 | missing/invalid credentials |
| `AuthorizationError` | 403 | authenticated but not permitted |
| `NotFoundError` | 404 | resource id does not exist |
| `ConflictError` | 409 | duplicate / version mismatch |
| `InternalError` | 500 | unexpected — log with stack trace |

### 4.2 Central error middleware
Every stack MUST have ONE global error handler that:
- Catches uncaught exceptions.
- Logs with: `timestamp`, `correlationId`, `route`, `method`, `errorClass`, `message`, `stack`.
- Returns the standard JSON envelope (see §6 of `spec.md`) — never a stack trace to the client.

### 4.3 Logging levels
`debug` | `info` | `warn` | `error` | `fatal`. Use the stack's canonical logger (Monolog, `logging`, `pino`, `Serilog`, `slog`, `tracing`, `std.log`).

---

## 5. Environment Variable / Config Handling

- **All secrets and env-specific values come from environment variables.** No secrets in source, no secrets in git.
- **Every sample ships a `.env.example`** listing every required key with a safe placeholder value and a one-line comment. Real `.env` is git-ignored.
- **Config is loaded once at boot** into a typed/validated struct or object; the rest of the code reads that object, not `process.env` / `os.Getenv` directly.
- **Fail fast on missing required keys** — the app MUST refuse to start with a clear error naming the missing variable.
- **DB driver is selected via env** (`DB_DRIVER=sqlite|postgres|mysql`). Connection string components (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`) are separate keys.
- **Required baseline keys for every sample:**
  ```
  APP_ENV=local|dev|staging|prod
  APP_PORT=8080
  APP_LOG_LEVEL=info
  DB_DRIVER=sqlite
  DB_HOST=
  DB_PORT=
  DB_NAME=
  DB_USER=
  DB_PASSWORD=
  DB_PATH=./data/app.db          # sqlite only
  UPLOAD_DIR=./storage/uploads
  UPLOAD_MAX_BYTES=10485760
  JWT_SECRET=                    # if auth enabled
  ```
- **No hard-coded ports, paths, or connection strings** anywhere in source.

---

## 6. Folder Structure for a REST Project

Canonical layered structure (see §1.3). Each layer has ONE responsibility:

- **routes/** — URL → controller wiring only. No logic.
- **controllers/** — parse request, call service, shape response. No DB, no business rules.
- **services/** — business logic. Stateless. Depends on repository interfaces, not concrete DB code.
- **repositories/** — the ONLY layer that talks to the DB driver. Returns models, not raw rows.
- **models/** — entities and DTOs. Plain data. Validation annotations allowed.
- **middleware/** — cross-cutting: auth, logging, correlation id, error handler, CORS.
- **config/** — env loading, DI wiring, DB driver selection.

Rule: **dependencies point inward** (routes → controllers → services → repositories → models). Never the reverse.

---

## 7. Testing Expectation

- **Every endpoint MUST have at least one integration test** hitting the real HTTP surface (in-process test server + in-memory or ephemeral SQLite).
- **Every service method MUST have a unit test** covering happy path + at least one error path.
- **Test naming:** `should_{expected}_when_{condition}` or the stack's idiom (`test_creates_student_when_payload_valid`).
- **AAA structure:** Arrange, Act, Assert — one logical assertion per test.
- **No network, no real filesystem outside a temp dir, no shared mutable state between tests.** Each test bootstraps and tears down its own DB.
- **Coverage floor:** 70% line coverage on `services/` and `controllers/`. Repositories covered by integration tests.
- **CI runs:** format check → lint → typecheck (where applicable) → unit tests → integration tests. All MUST pass before merge.
- **Fixtures** live under `tests/fixtures/` as JSON, following the encoding rules in `spec/19-main-worker-service/fixtures/conventions.md` (UTF-8, LF, 2-space indent, PascalCase keys, RFC 3339 timestamps on the wire).

---

## 8. Review Checklist (apply to every PR and every AI-generated sample)

- [ ] Folder/file names match §1.
- [ ] Formatter + linter clean.
- [ ] Naming matches §3 for the stack.
- [ ] No `catch` without log; no stringified errors at boundaries.
- [ ] Global error middleware returns the standard envelope.
- [ ] No secrets or connection strings in source; `.env.example` updated.
- [ ] Layered folder structure respected; no DB calls in controllers.
- [ ] Tests added for new endpoints and service methods; CI green.
- [ ] `README.md` updated if public surface changed.

---

*Any deviation from this document requires an explicit note in the sample's `README.md` under a "Deviations" heading, with justification.*
