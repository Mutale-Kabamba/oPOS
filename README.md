
# Finance Book System

Finance Book is a web-based accounting and financial management system for small businesses and organizations. It supports two main roles: **Admin** and **Accountant**. The system enables secure transaction recording, reporting, user management, and PDF export of key financial documents.

---

## Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation & Build Guide](#installation--build-guide)
- [Environment Setup](#environment-setup)
- [Running the Application](#running-the-application)
- [User Manual](#user-manual)
- [Troubleshooting](#troubleshooting)
- [Best Practices](#best-practices)
- [Admin Handover Checklist](#admin-handover-checklist)

---

## Features

- Secure login, password reset, and email verification
- Role-based dashboards (Admin, Accountant)
- Transaction recording and editing
- Chart of Accounts (COA) and Category management
- User management (Admin only)
- PDF export for reports and logs
- Statutory reminders (PAYE, Turnover Tax, NAPSA)
- Audit logging and user activity tracking

---

## System Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- Database: MySQL or SQLite (default)

---

## Installation & Build Guide

### 1. Clone the Repository

```
git clone https://github.com/Mutale-Kabamba/finance-book.git
cd finance-book
```

### 2. Backend Setup (Laravel)

Install PHP dependencies:

```
composer install
```

Copy the environment file and set your variables:

```
cp .env.example .env
# Edit .env as needed (DB, MAIL, etc.)
```

Generate application key:

```
php artisan key:generate
```

Run database migrations:

```
php artisan migrate --force
```

### 3. Frontend Setup (Vite, Tailwind, Alpine.js)

Install Node dependencies:

```
npm install
```

Build frontend assets:

```
npm run build
```

### 4. Storage Link (for profile photos, etc.)

```
php artisan storage:link
```

---

## Environment Setup

Edit your `.env` file for database, mail, and other settings. Example for SQLite:

```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

Or for MySQL:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finance_book
DB_USERNAME=youruser
DB_PASSWORD=yourpass
```

---

## Running the Application

Start the Laravel backend server:

```
php artisan serve
```

Start the Vite development server (for hot-reload):

```
npm run dev
```

Or run both with:

```
npx concurrently "php artisan serve" "npm run dev"
```

---

## User Manual

### Roles

- **Admin**: Full access to all data, user management, settings, and reports.
- **Accountant**: Access to own transactions, reports, and profile.

### Login & Security

- Login with email and password
- Forgot password: reset via email link
- Email verification may be required
- Only active users can access the system

### Main Navigation

- Dashboard
- Ledger (Transaction Report)
- Reports (Income Statement, Balance Sheet)
- Settings (Profile, COA, Categories, User Management)

### Accountant Guide

- **Dashboard**: View monthly income, costs, profit, and recent transactions
- **Record Entry**: Add new transactions (income, expense, asset, liability)
- **Edit/Delete**: Only own transactions
- **Ledger**: Search, filter, export PDF
- **Reports**: Income Statement, Balance Sheet (own data)
- **Profile**: Update info, photo, password, delete account

### Admin Guide

- **Dashboard**: System-wide totals, quick actions
- **Ledger/Reports**: View/edit/delete all transactions, export all reports
- **Settings**: Manage COA, Categories, Users, Profile
- **User Management**: Create, edit, deactivate, delete users
- **Activity Monitoring**: View/export user activity logs

### PDF Export

- Export available for all main reports and logs
- Footer branding: Ori Studio Limited, KT4C System V1.26.1

---

## Troubleshooting

- **Account deactivated**: Contact admin to reactivate
- **Cannot delete COA account**: Must have no transactions; deactivate instead
- **No transactions in report**: Check filters/date range
- **PDF display issues**: Use a standards-compliant PDF viewer

---

## Best Practices

- Use clear descriptions for entries
- Reconcile entries daily
- Assign minimal roles
- Prefer deactivation over deletion for users/accounts
- Export reports regularly for audit

---

## Admin Handover Checklist

1. Create user with correct role
2. Verify login
3. Confirm menu access
4. User sets password/profile
5. Confirm first transaction and report export

---

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
