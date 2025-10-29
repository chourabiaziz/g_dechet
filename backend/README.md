# Backend (Django REST) — g_dechet

This folder contains a minimal Django + Django REST Framework skeleton intended to reuse the existing PostgreSQL schema from the monolith.

Quick start (Windows / PowerShell)

1. Create a virtualenv and install dependencies

```powershell
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

2. Configure database connection in `.env` (see `.env.example`). By default the project expects Postgres on the same host as the main repo (you can reuse the current docker-compose Postgres):

Example `.env` keys:

```
DATABASE_URL=postgresql://app:!ChangeMe!@127.0.0.1:5432/app
DJANGO_SECRET_KEY=your-secret
```

3. Reuse the current DB schema

This skeleton intentionally does not provide ready-made models for all tables. To generate Django models from the current DB schema, run:

```powershell
python manage.py inspectdb > accounts/models_inspect.py
```

Then move and clean the generated models into `accounts/models.py` and set `class Meta: managed = False` for existing tables if you do not want Django migrations to manage them yet.

4. Run the dev server (after creating models or when you are ready to use the API):

```powershell
python manage.py runserver 0.0.0.0:8001
```

Notes

- This skeleton contains a basic `accounts` app with a registration API and serializer that you can adapt to the inspected models.
- If you prefer a migration-based approach from day 1, generate Django models and create initial migrations after mapping the entities.
