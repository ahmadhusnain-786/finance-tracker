# FinanceTracker

A personal finance management web application built with **Laravel 13**, **MySQL 8**, **Bootstrap 5**, and **Chart.js**.

Every registered user gets a private space to track income, expenses, budgets, savings goals, and financial reports — completely isolated from other users.

---

## Features

### User
- Dashboard with date-filtered KPIs (income, expenses, balance, savings rate)
- Charts: income vs expenses, expense-by-category doughnut, cumulative savings trend
- Income & expense tracking with categories, payment methods, and recurring flags
- Unified transaction history with filters (type, category, payment, date range, amount)
- Budgets with progress bars and warning levels (80% / 100%)
- Savings goals with contributions and auto-completed status
- Recurring transactions processed by scheduler
- Notifications: budget warnings, exceeded alerts, goal deadlines
- 8 reports: income, expense, monthly, yearly, category, budget, savings, financial summary
- Exports: CSV, Excel (.xlsx), PDF
- Categories and payment methods with full CRUD
- Profile and Settings (currency, timezone, date format, avatar)
- Dark mode (persisted)
- Responsive UI

### Admin
- System dashboard (user counts, activity summaries)
- User management: search, filter, view, activate/deactivate, change role
- Default (system) category management
- System settings (app name, defaults, registration toggle)

### Security
- CSRF, XSS-safe Blade, mass-assignment protection
- Password hashing (bcrypt, 12 rounds)
- Strong password rule (10+ chars, mixed case, number, symbol)
- Rate limiting on auth + export routes
- Session encryption
- Security headers (CSP, X-Frame-Options, X-Content-Type-Options, etc.)
- User-isolated financial data (no admin override on financials)
- Activity log for sensitive actions
- Deactivated-user blocking via middleware

### Performance
- Versioned per-user cache with automatic invalidation via observers
- Indexed database columns
- Eager loading to avoid N+1 queries
- Pagination on all list pages

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.5+, Laravel 13 |
| Database | MySQL 8 |
| Frontend | Blade + Bootstrap 5 + Bootstrap Icons + vanilla JS |
| Charts | Chart.js 4 |
| Excel export | maatwebsite/excel 4 |
| PDF export | barryvdh/laravel-dompdf 3 |
| Tests | PHPUnit (Laravel testing tools) |

---

## Requirements

- PHP 8.4 or higher (8.5 tested)
- Composer 2
- MySQL 8 (or MariaDB 10.6+)
- Node.js LTS (optional — only for asset builds later)
- Windows / macOS / Linux

---

## Installation

```bash
git clone <your-repo-url> finance-tracker
cd finance-tracker

composer install
cp .env.example .env
php artisan key:generate