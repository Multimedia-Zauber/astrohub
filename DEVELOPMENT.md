# AstroHub Development

This document describes the first 0.1 Alpha development stack.

## Requirements

- PHP 8.2+ with PDO SQLite
- Composer
- Python 3.11+
- Node.js 20+ and npm

## 1. Science service

```bash
cd science
python -m venv .venv
```

Activate the virtual environment, then:

```bash
pip install -r requirements.txt
uvicorn app.main:app --host 127.0.0.1 --port 8090 --reload
```

Check `http://127.0.0.1:8090/health`.

## 2. Core API

```bash
cd core
composer install
composer serve
```

Check `http://127.0.0.1:8080/api/v1/health`.

The first request creates the local SQLite database under `runtime/astrohub.sqlite`.

## 3. Web UI

```bash
cd apps/web
npm install
npm run dev
```

Open `http://127.0.0.1:5173`.

The foundation screen shows whether Core, SQLite and the Science service are reachable.

## Foundation ports

| Service | Address |
| --- | --- |
| Web/PWA | `127.0.0.1:5173` |
| Core API | `127.0.0.1:8080` |
| Science | `127.0.0.1:8090` |

These defaults are for local development only.

## Architecture rule

The browser talks to AstroHub Core. Core owns persistence and application workflows. Scientific calculations are delegated to the local Science service. External astronomy providers will be accessed through provider interfaces rather than directly from UI components.
