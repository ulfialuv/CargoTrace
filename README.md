# CargoTrace

Shipment tracking and exception management for logistics operations, built with **Laravel 13** (PHP 8.3+) and MySQL.

CargoTrace answers three questions for the people who move cargo: *where is my shipment, is it late, and what went wrong?*
Operators record tracking events and exceptions, administrators manage master data and users, and customers follow only their own shipments on a timeline and a map.

> Final project for Web Programming 2. All demo data is synthetic.

## Features

| Area | What it does |
|---|---|
| **Roles & access** | Three roles (admin, operator, customer). Access is enforced on the server by middleware and per-user data scoping, not only by hiding buttons. |
| **Shipment lifecycle** | `CREATED → BOOKED → PICKED_UP → IN_TRANSIT → DELIVERED`. Only the next status is allowed, `DELIVERED` is terminal, and pickup requires payment when the payment module is active. |
| **Tracking** | Historical tracking events with location, coordinates, time, description and author. Timeline view and route on a Leaflet / OpenStreetMap map. |
| **ETA & delay** | Compares the current ETA (`estimated_arrival`) with the promised date (`expected_arrival`). Delay duration, severity and the reason are shown. Threshold is configurable. |
| **Exceptions** | `CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`, `DAMAGED`, `MISROUTED`, `DOCUMENTATION_ISSUE` with severity, `OPEN / INVESTIGATING / RESOLVED` status, detection and resolution time, and a change history. Delays create and close an exception automatically. |
| **Dashboard** | Shipment counts by status, exceptions and charts (Chart.js). |
| **REST API** | Token authentication with Laravel Sanctum, consistent JSON envelope, pagination, filters. |
| **Audit log** | Records who did what and when: create / update / delete of shipments, exceptions, carriers, locations and users, shipment status changes, and login / logout (web and API), with old and new values. Passwords are never stored. Admin only. |
| **Extras** | Weather and geocoding (Open-Meteo, Nominatim), currency and world time, news (GNews, optional), in-app notifications. |

Weather is shown as context only; it is never used to predict delays.

## Roles

| | Admin | Operator | Customer |
|---|:-:|:-:|:-:|
| Manage users, carriers, locations | ✅ | – | – |
| Create / edit shipments, record tracking, change status | ✅ | ✅ | – |
| Report and update exceptions | ✅ | ✅ | – |
| Delete shipments | ✅ | – | – |
| View shipments, tracking, exceptions | all | all | **own only** |

## Architecture

```
Browser / API client
   → Routes + Middleware        (auth, role)
   → Controllers                (HTTP only: validate, call a service, shape the response)
   → Services                   (business rules and transactions)
   → Eloquent models → MySQL
```

| Service | Responsibility |
|---|---|
| `ShipmentService` | create / update / delete rules, shipment numbers |
| `TrackingService` | status transitions, tracking events, payment gate |
| `EtaService` | deterministic ETA vs expected-arrival rule, severity, human-readable reason |
| `ExceptionService` | exception lifecycle with history, automatic delay exceptions |
| `AuditLogService` | writes audit log entries (called by `AuditObserver` for model changes and by the login/logout listeners) |

The REST API controllers (`app/Http/Controllers/Api`) already use these services. Some older web controllers still contain their own logic and are being migrated.

## Getting started

Requirements: PHP 8.3+, Composer, MySQL, and the PHP extensions `pdo_mysql`, `mbstring`, `openssl`. (With [Laragon](https://laragon.org) these are included.)

```bash
git clone <your-repository-url> cargotrace
cd cargotrace

composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Create an empty MySQL database named `cargotrace` (or change `DB_*` in `.env`), then:

```bash
php artisan migrate
php artisan db:seed           # demo accounts, carriers, locations, shipments, exceptions
php artisan serve             # http://127.0.0.1:8000
```

`npm install && npm run build` is only needed for the Vite-based welcome page.

### Demo accounts

`php artisan db:seed` is safe to run repeatedly: it never overwrites an existing account's password and only recreates the `CT-DEMO-xxx` shipments. It does nothing when `APP_ENV=production`.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@cargotrace.com` | value of `DEMO_PASSWORD` in `.env` (default `password`) |
| Operator | `operator1@cargotrace.test`, `operator2@cargotrace.test` | same |
| Customer | `customer1@cargotrace.test` … `customer3@cargotrace.test` | same |

If `admin@cargotrace.com` already exists in your database, its current password is kept.

The seeded data covers every lifecycle status, three delayed shipments (LOW / MEDIUM / HIGH), open, investigating and resolved exceptions, and an on-time delivery, so each screen has something to show.

## ETA and delay rule

```
delay      = estimated_arrival (current ETA) − expected_arrival (promised date)
delayed    = delay > CARGOTRACE_DELAY_THRESHOLD_HOURS
severity   = LOW     if delay ≤ 6 h
             MEDIUM  if 6 h < delay ≤ 24 h
             HIGH    if delay > 24 h
```

Differences are computed in whole minutes. When a shipment becomes delayed an `[AUTO-DELAY]` exception is created; it is updated if the severity changes, and closed automatically when the shipment is back on time or delivered. Every shipment returned by the API includes a `delay` object with the values used and a plain-language reason.

The threshold and the severity bands are configured in `.env` (see `config/cargotrace.php`), so each group can use its own scenario:

```
CARGOTRACE_DELAY_THRESHOLD_HOURS=12
CARGOTRACE_DELAY_LOW_MAX_HOURS=6
CARGOTRACE_DELAY_MEDIUM_MAX_HOURS=24
```

Run `php artisan config:clear` after changing them.

## REST API

Base path `/api` (the same routes are also available under `/api/v1`). Authenticate with a Sanctum bearer token.

```bash
# 1. log in and copy data.token from the response
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"operator1@cargotrace.test","password":"password"}'

# 2. call an endpoint
curl http://127.0.0.1:8000/api/shipments -H "Accept: application/json" -H "Authorization: Bearer <token>"
```

| Method | Endpoint | Roles |
|---|---|---|
| POST | `/register`, `/login` | public (register always creates a customer) |
| POST / GET | `/logout`, `/user` | any logged-in user |
| GET | `/shipments`, `/shipments/{id}` | all (customer: own only) |
| POST | `/shipments` | admin, operator |
| PUT / PATCH | `/shipments/{id}` | admin, operator |
| PATCH | `/shipments/{id}/status` | admin, operator |
| DELETE | `/shipments/{id}` | admin |
| GET / POST | `/shipments/{id}/tracking` | read: all (own only); write: admin, operator |
| GET / POST | `/shipments/{id}/exceptions` | read: all (own only); write: admin, operator |
| GET | `/exceptions`, `/exceptions/{id}` | all (customer: own only) |
| POST | `/exceptions` | admin, operator |
| PUT / PATCH | `/exceptions/{id}` | admin, operator |
| GET | `/carriers`, `/locations` (+ `/{id}`) | all |
| POST / PUT / DELETE | `/carriers`, `/locations` | admin |

Filters and pagination: `?page=`, `?per_page=` (max 50), `?status=`, `?search=` and more per resource.

**Response format**

```json
{ "success": true,  "message": "OK", "data": { }, "meta": { "current_page": 1, "per_page": 15, "total": 42, "last_page": 3 } }
{ "success": false, "message": "Data yang dikirim tidak valid.", "errors": { "status": ["..."] } }
```

| Code | Meaning |
|---|---|
| 200 / 201 | success / created |
| 401 | missing or invalid token |
| 403 | role not allowed, or someone else's data |
| 404 | not found |
| 409 | conflict (e.g. deleting a paid shipment or a carrier in use) |
| 422 | validation error or invalid status transition |
| 429 | rate limited |

A Postman collection is included in `docs/` (import it, set `base_url`, run **Auth → Login** first).

## Testing

```bash
php artisan test
```

Tests run on an in-memory SQLite database (enable the `pdo_sqlite` PHP extension), so your MySQL data is never touched. They cover authentication, role and ownership rules, shipment CRUD, tracking and invalid status transitions, ETA/delay severity boundaries and thresholds, automatic delay exceptions, the web role restrictions, and the demo seeder.

## Project structure

```
app/Http/Controllers/Api   REST API controllers (thin)
app/Http/Resources         JSON resources
app/Services               business rules
app/Exceptions             BusinessRuleException
config/cargotrace.php      delay rule + demo password settings
database/seeders           DemoSeeder, AdminSeeder
routes/web.php             web UI (admin group is protected by role:admin)
routes/api.php             REST API
tests/                     Unit + Feature tests
```

## Security notes

- Roles are enforced server-side (`role:` middleware) and customers are scoped to their own records.
- Registration through the API can only create customers.
- Login endpoints are rate limited.
- `.env` is git-ignored. Never commit real credentials, and never run the demo seeder against production data.

## Roadmap

- [ ] ERD and data dictionary (`docs/`)
- [ ] GitHub Actions CI (run tests on every push)
- [ ] Deployment
- [ ] Move the remaining web controllers onto the service layer
- [ ] Normalise `carrier`, `origin` and `destination` into foreign keys, and add `shipment_items`
