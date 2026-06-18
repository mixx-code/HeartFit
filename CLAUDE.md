# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**HeartFit** is a Laravel 12 meal subscription and delivery management system. It handles package ordering, Midtrans payment processing, delivery tracking, and role-based admin dashboards. The app runs on XAMPP (Apache + MySQL) locally.

## Common Commands

```bash
# Development (runs 4 processes concurrently: server, queue, logs, Vite)
composer run dev

# Individual processes
php artisan serve               # Dev server at http://127.0.0.1:8000
php artisan queue:listen --tries=1
php artisan pail --timeout=0    # Live log streaming
npm run dev                     # Vite asset compilation

# Database
php artisan migrate
php artisan migrate --seed
php artisan storage:link

# Testing
php artisan test                # Runs PHPUnit (SQLite in-memory)
php artisan config:clear && php artisan route:clear

# Custom command
php artisan heartfit:generate-delivery-statuses [--date=YYYY-MM-DD] [--all]
```

## Architecture

### Role System

Roles are stored as plain strings on `users.role`. The `CheckRole` middleware (`app/Http/Middleware/CheckRole.php`) normalizes them (lowercase, spaces/hyphens → underscores) and supports comma/pipe-separated lists: `middleware('role:admin,superadmin')`.

Active roles: `superadmin`, `admin`, `ahli_gizi`, `kurir`, `customer`.

Route groups in `routes/web.php` are structured around these roles:
- **Admin/Superadmin** (`/dashboard/admin`) — staff CRUD, packages, menus, orders
- **Customer** (`/dashboard/customer`) — order creation, payment, account
- **Ahli Gizi** — view orders by date, WhatsApp redirect
- **Kurir** — delivery status updates

Superadmin can create `admin` accounts; admin can only create `ahli_gizi` accounts.

### Authentication

Login accepts email or username (distinguished by presence of `@`). On success, `login_time_ts` is stored in session. `SessionTimeout` middleware enforces an absolute 1-hour limit. Routes use `middleware(['auth', 'session.timeout'])`.

### Order Flow

1. `BlockOrderWindowFromDB` middleware blocks new orders if the customer already has an active subscription covering today or tomorrow.
2. Customer creates order → Midtrans Snap token generated via `MidtransService`.
3. Payment confirmed via webhook (`POST /midtrans/webhook`, excluded from CSRF) or polling (`/customer/orders/{order}/check-payment`).
4. Delivery rows generated per-day via the `heartfit:generate-delivery-statuses` command.

### Key Models

- **Order** — stores `service_dates` and `unique_menus` as JSON arrays, prices as integers (Rupiah). Status enum: `UNPAID`, `PAID`, `CANCELED`, `EXPIRED`, `REFUNDED`.
- **MenuMakanan** — `serve_days` and `foto_makanan` are JSON-cast arrays. Photos stored as JSON objects (converted from base64).
- **UserDetail** — `foto_ktp_base64` is encrypted at rest. Has `mr` (Medical Record number, unique).
- **User** — soft deletes with `created_by`, `updated_by`, `deleted_by` audit trail.

### Service Layer

- `app/Services/MidtransService.php` — initializes Midtrans config from `.env`.
- `app/Services/PdfService.php` — wraps Dompdf; renders views to PDF/receipt streams.
- `app/Services/MRGeneratorService.php` — generates unique MR numbers (format: `DDMMYYYY` + 3-digit sequence).

### Package Pricing

Prices are hardcoded in `config/heartfit.php` (not in the database). Keys follow the pattern `{type}_{duration}`, e.g., `reguler_bulanan`, `premium_mingguan`.

### Bootstrap

Laravel 12 uses `bootstrap/app.php` (no `Kernel.php`). Middleware aliases are registered there:
- `role` → `CheckRole`
- `session.timeout` → `SessionTimeout`
- `block.order.window.db` → `BlockOrderWindowFromDB`

## Environment Notes

- Database: MySQL, DB name `heartfit`, user `root`, no password.
- Sessions and queues use the database driver.
- Broadcasting uses Reverb (WebSocket on port 8080).
- Midtrans is in sandbox mode (`MIDTRANS_IS_PRODUCTION=false`).
- Tests use SQLite in-memory; no MySQL needed for `php artisan test`.
