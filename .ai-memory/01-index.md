# Repository Context & AI Architecture Index

> **Repository:** `laravel-automation`
> **Version:** 1.0.0
> **Target:** `.ai-memory/01-index.md`
> **Purpose:** Master repository index, directory router, and operational architecture guide for AI agents.

---

## 1. Repository Architecture Overview

This repository is an automation dashboard and server orchestration system built with:
- **Frontend (`src/`):** TanStack Start, React 19, TypeScript, Tailwind CSS v4, Lucide React, and Radix UI primitives.
- **Backend API (`be-rest/`):** Laravel REST backend providing task runner services, automations, and system integration.
- **Formal Specifications (`02-spec/`):** Comprehensive coding standards, error management policies, and database conventions aligned with the Prompt Architect center way.
- **Canonical Prompts (`01-prompts/`):** Reusable prompts including reading workflows, planning loops, and coding standards.
- **AI Agent Capabilities (`.agents/skills/`):** Native Antigravity skills for memory ingestion, parent task orchestration, and quality verification.
- **Institutional Memory (`.ai-memory/`):** Persistent project memory, active plans, issue logs, and historical resolutions.

---

## 2. Directory Navigation Router

| Location | Purpose | Key Entrypoint |
|---|---|---|
| `02-spec/` | Formal cross-language specifications & standards | `02-spec/spec-index.md` |
| `01-prompts/` | Canonical execution prompts & workflows | `01-prompts/03-read-write/` |
| `.agents/skills/` | Installed Antigravity agent skills | `.agents/skills/` |
| `.ai-memory/memory/` | Institutional knowledge base & conventions | `.ai-memory/memory/01-index.md` |
| `.ai-memory/plans/` | Active roadmap, parent task specs & subtasks | `.ai-memory/plans/01-index.md` |
| `.ai-memory/what-to-read.md` | Authoritative reading order for agents | `.ai-memory/what-to-read.md` |
| `.ai-memory/strictly-avoid.md` | Hard prohibitions & CODE RED constraints | `.ai-memory/strictly-avoid.md` |
| `be-rest/` | Laravel REST backend application | `be-rest/` |
| `src/` | TanStack Start frontend application | `src/` |

---

## 3. Core Universal Rules (CODE RED)

1. **Strict Read-Only Reading Phase:** Reading workflows are strictly zero-write to the repository. Any scratchpad or agent communication must be isolated to `%TEMP%/laravel-automation/`.
2. **Never Swallow Errors:** Every error must be explicitly handled or wrapped with application fault types.
3. **Implicit Positive Booleans:** NEVER compare booleans explicitly against `true` (`if (isReady)` mandatory; `if (isReady == true)` forbidden).
4. **Mandatory Boolean Prefixes:** All boolean variables, fields, and functions MUST use positive prefixes (`is`, `has` only).
5. **Strict Relative Git Paths:** All markdown links and references must be relative to repository root. Never use absolute paths or `file:///` URIs.
6. **Strict Lowercase File Naming:** All files, scripts, and documentation must use lowercase naming (`readme.md`, `agents.md`).
