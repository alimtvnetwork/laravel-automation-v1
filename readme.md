# Laravel Automation

> Modern automation dashboard and orchestration interface powered by TanStack Start, React 19, Tailwind CSS, and a Laravel REST backend API (`be-rest/`).

## Architecture & Overview

- **Frontend:** TanStack Start (`src/`), React 19, TypeScript, Vite, Tailwind CSS v4, Lucide React, Shadcn/Radix UI components.
- **Backend:** Laravel REST API service (`be-rest/`) orchestrating server automation, workflows, and task execution.
- **Specifications:** Canonical specifications organized under `02-spec/`.
- **AI Memory & Operations:** Persistent AI memory and operations tracking organized under `.ai-memory/`.
- **Coding Guidelines:** Fully aligned with Prompt Architect global guidelines and cross-language coding conventions.

## Directory Structure

```text
laravel-automation/
├── .agents/                    # Antigravity agent skills, rules, and scripts
├── .ai-memory/                 # Institutional memory, active plans, issue logs
├── 01-prompts/                 # Canonical execution and prompt library
├── 02-spec/                    # Authoritative architectural specifications
├── be-rest/                    # Laravel REST API backend service
├── public/                     # Static assets and icons
├── src/                        # TanStack Start frontend application
├── AGENTS.md                   # Global AI agent operating rules
├── package.json                # Project dependencies and script declarations
├── readme.md                   # Project identity and architecture documentation
└── vite.config.ts              # Vite & TanStack Start build configuration
```

## Quick Start

```bash
# Install frontend dependencies
bun install   # or npm install

# Run frontend development server
bun dev       # or npm run dev

# Build frontend production bundle
bun build     # or npm run build
```

## AI Agent Integration

Before initiating tasks or code modifications, agents must execute the memory ingestion workflow:
1. Review [AGENTS.md](AGENTS.md) for global constraints (boolean standards, zero-storage mandate, lowercase filenames, relative git paths).
2. Read [.ai-memory/what-to-read.md](.ai-memory/what-to-read.md) first as the authoritative reading priority list.
3. Use high-performance reading protocols with zero repository writes during analysis.
