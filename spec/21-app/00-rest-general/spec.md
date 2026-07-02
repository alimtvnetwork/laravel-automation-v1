# General REST Specification

**Version:** 1.0.0
**Scope:** Stack-agnostic — applies to every reference project under `be-rest/` (Laravel, Python, Node, .NET, Golang, Rust, Zig, WordPress).
**Status:** Draft

---

## 1. Purpose

Defines the shared REST contract, database configurability, standard endpoints, response format, and auth research hooks that every stack-specific reference sample must implement identically. Stack folders inherit this spec; stack-specific specs (e.g. `spec/21-app/01-laravel/`) only document deviations or stack-idiomatic implementation details.

---

## 2. REST Conventions

### 2.1 Resource naming

- Resource endpoint names are **plural** nouns, lowercase, kebab-case for multi-word resources.
  - ✅ `/students`, `/file-upload`, `/file-serve`
  - ❌ `/student`, `/getStudents`, `/Students`
- No trailing slash.
- IDs are path parameters: `/students/{id}`.
- No verbs in resource paths (verbs are expressed via HTTP method).

### 2.2 Standard CRUD verbs

| Method | Path              | Purpose                        | Success status |
|--------|-------------------|--------------------------------|----------------|
| GET    | `/students`       | List all students              | 200            |
| GET    | `/students/{id}`  | Fetch a single student         | 200            |
| POST   | `/students`       | Create a new student           | 201            |
| PUT    | `/students/{id}`  | Full replacement of a student  | 200            |
| PATCH  | `/students/{id}`  | Partial update of a student    | 200            |
| DELETE | `/students/{id}`  | Delete a student               | 200 or 204     |

Rules:
- `PUT` replaces the whole resource; `PATCH` updates only supplied fields.
- `DELETE` is idempotent — repeating it on a missing resource returns 404, never 500.
- `GET` is safe and side-effect-free.

---

## 3. Database of Choice

The reference project must support three database backends, selected at runtime via environment variables. Nothing is hardcoded.

### 3.1 Supported backends

| Backend    | Default use            |
|------------|------------------------|
| SQLite     | Local development (default) |
| MySQL      | Optional production target |
| PostgreSQL | Optional production target |

### 3.2 Required environment variables

```
DB_DRIVER=sqlite | mysql | postgres
DB_HOST=
DB_PORT=
DB_NAME=
DB_USER=
DB_PASSWORD=
DB_SSL=true|false
```

- `DB_DRIVER=sqlite` uses a local file path from `DB_NAME` (e.g. `./data/app.sqlite`); host/port/user/password are ignored.
- Missing `DB_DRIVER` defaults to `sqlite`.
- The stack must fail fast at startup if required vars for the selected driver are missing.
- No credentials in source. `.env.example` files ship with placeholders only.

---

## 4. Standard Endpoints

Every reference sample must implement these endpoints identically.

### 4.1 `/students` — full CRUD

Follows §2.2 exactly. Student resource fields (reference schema):

| Field       | Type    | Constraints             |
|-------------|---------|-------------------------|
| `id`        | int     | PK, server-assigned     |
| `name`      | string  | required, 1–120 chars   |
| `email`     | string  | required, unique, email |
| `createdAt` | string  | ISO 8601, server-set    |
| `updatedAt` | string  | ISO 8601, server-set    |

### 4.2 `/file-upload`

**Method:** `POST /file-upload` (multipart/form-data)

**Spec:**
- Accepted MIME types: `image/png`, `image/jpeg`, `image/webp`, `application/pdf`.
- Max size: **10 MB** per file.
- Required form fields:
  - `file` — the binary payload
  - `ownerId` — string, references the uploading user/entity

**Acceptable (success):**
- MIME type is in the allowed list.
- Size ≤ 10 MB.
- All required fields present.
- Storage write succeeds.
- Response: `201 Created` with `{ status, data: { fileId, filename, size, mime, url } }`.

**Reject (failure):**

| Condition                       | HTTP | `error.code`            |
|---------------------------------|------|-------------------------|
| Missing `file` or `ownerId`     | 422  | `VALIDATION_ERROR`      |
| Unsupported MIME type           | 415  | `UNSUPPORTED_MEDIA_TYPE`|
| File exceeds 10 MB              | 413  | `PAYLOAD_TOO_LARGE`     |
| Not authenticated               | 401  | `UNAUTHORIZED`          |
| Authenticated but not permitted | 403  | `FORBIDDEN`             |
| Storage failure                 | 500  | `STORAGE_ERROR`         |

Error responses use the standard envelope (§5).

### 4.3 `/file-serve`

**Method:** `GET /file-serve/{fileId}`

- **Auth:** Required. Caller must be authenticated and either own the file or hold a role permitting access.
- **Response type:** raw binary stream with correct `Content-Type` (from stored MIME) and `Content-Disposition: inline; filename="..."`.
- **Expiry:** Files do not expire by default. If a signed short-lived URL is issued (optional feature), it expires in **15 minutes** and is single-scope (read-only, that `fileId` only).
- **Errors:** `401` if unauthenticated, `403` if not permitted, `404` if `fileId` unknown, `500` on storage read failure — all via the standard JSON envelope (except the 200 binary success).

---

## 5. Response Format

### 5.1 Envelope

All JSON responses use one consistent shape:

```json
{
  "status": "success" | "error",
  "data": { ... } | [ ... ] | null,
  "message": "human-readable summary",
  "error": {
    "code": "MACHINE_READABLE_CODE",
    "details": { ... }
  }
}
```

Rules:
- `status` is always present.
- On success: `data` is populated, `error` is `null` or omitted.
- On failure: `error.code` is populated, `data` is `null` or omitted, `message` is human-readable.
- Binary responses (`/file-serve` success) are the sole exception — no envelope.

### 5.2 HTTP status codes

| Code | Use                                              |
|------|--------------------------------------------------|
| 200  | Successful GET/PUT/PATCH/DELETE                  |
| 201  | Successful POST that creates a resource          |
| 204  | Successful DELETE with no body (optional)        |
| 400  | Malformed request (syntax, bad JSON)             |
| 401  | Missing or invalid authentication                |
| 403  | Authenticated but not authorized                 |
| 404  | Resource not found                               |
| 413  | Payload too large                                |
| 415  | Unsupported media type                           |
| 422  | Validation failed (well-formed but semantically invalid) |
| 500  | Unhandled server error                           |

---

## 6. Auth — Research Hooks

Not implemented in the initial scaffolding. Documented here so every stack knows where they will plug in.

### 6.1 OAuth 2.0

- **Use case:** third-party and service-to-service authentication.
- **Plug-in point:** middleware layer in front of `/students`, `/file-upload`, `/file-serve`.
- **Research to complete before implementation:**
  - Chosen grant types (client credentials for service-to-service; authorization code + PKCE for third-party UIs).
  - Token introspection vs. self-contained JWT access tokens.
  - Provider strategy: managed (Auth0, Clerk, Cognito) vs. self-hosted (Keycloak, Ory Hydra).
  - Scope naming convention aligned with resource names (`students:read`, `students:write`, `files:read`, `files:write`).

### 6.2 JWT

- **Use case:** end-user session auth for first-party clients.
- **Plug-in point:** same middleware layer as OAuth; JWT is the token format.
- **Research to complete before implementation:**
  - Signing algorithm (`RS256` preferred over `HS256` for multi-service).
  - Key rotation and JWKS endpoint.
  - Access-token TTL (short, e.g. 15 min) + refresh-token strategy.
  - Claim schema: `sub`, `iat`, `exp`, `roles`, `tenantId`.
  - Revocation model (denylist vs. short TTL only).

Both mechanisms must be swappable via configuration; endpoint handlers must never inspect tokens directly — they consume a resolved `principal` from middleware.

---

## 7. Cross-References

- Stack-specific specs: `spec/21-app/01-laravel/` … `spec/21-app/08-wordpress/`
- Reference projects: `be-rest/{stack}/`
- Response/error conventions align with `spec/03-error-manage/` and `spec/04-database-conventions/06-rest-api-format.md`.
