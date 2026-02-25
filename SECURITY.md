# Security Policies & Documentation

## Sanasa Dormitory Management System

**Version:** 1.0  
**Date:** February 26, 2026  
**Framework:** Laravel 12 (PHP 8.2)  
**Database:** Microsoft SQL Server

---

## Table of Contents

1. [Password Policy](#1-password-policy)
2. [Login Attempt Policy](#2-login-attempt-policy)
3. [Data Handling Policy](#3-data-handling-policy)
4. [Access Control Policy](#4-access-control-policy)
5. [Logging & Monitoring Policy](#5-logging--monitoring-policy)
6. [Backup & Recovery Policy](#6-backup--recovery-policy)
7. [Incident Response Plan](#7-incident-response-plan)

---

## 1. Password Policy

### Requirements

All user passwords must meet the following minimum requirements, enforced by the system at registration and password update:

| Requirement          | Rule                                             |
| -------------------- | ------------------------------------------------ |
| Minimum length       | 8 characters                                     |
| Must contain letters | At least one alphabetic character                |
| Must contain numbers | At least one numeric digit                       |
| Must contain symbols | At least one special character (e.g. `!@#$%`)    |
| Confirmation         | Password must be entered twice (confirmed match) |

### Implementation

Password rules are enforced globally in `app/Providers/AppServiceProvider.php` using Laravel's `Password::defaults()`:

```php
Password::defaults(function () {
    return Password::min(8)->letters()->numbers()->symbols();
});
```

All controllers that create or update user credentials call `Password::defaults()` in their validation rules, ensuring consistent enforcement across the application.

### Storage

- Passwords are **never stored in plain text**
- All passwords are hashed using **bcrypt** with **12 rounds** (`BCRYPT_ROUNDS=12`)
- Laravel's `User` model casts the `password` field as `hashed`, adding an additional layer of security

---

## 2. Login Attempt Policy

### Rate Limiting

To prevent brute-force attacks, the login endpoint is protected by Laravel's built-in throttle middleware:

- **Maximum attempts:** 5 per minute per IP address
- **Lockout duration:** 1 minute (automatic, no manual unlock needed)
- **HTTP response on lockout:** `429 Too Many Requests`

### Implementation

Applied directly on the POST `/login` route in `routes/web.php`:

```php
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
```

### Additional Protections

- **Session regeneration** on every successful login (prevents session fixation attacks)
- **Session invalidation** on logout (old session token is destroyed)
- **CSRF token regeneration** on logout
- **Archived account check** — deactivated accounts are blocked immediately after authentication attempt

---

## 3. Data Handling Policy

### Data Collected

The system collects and processes the following personal and financial data:

| Data Type            | Examples                                         | Who Can Access                    |
| -------------------- | ------------------------------------------------ | --------------------------------- |
| Tenant personal info | Name, address, contact number, emergency contact | Owner, Caretaker                  |
| Financial records    | Invoices, payments, security deposits, refunds   | Owner, Caretaker                  |
| Room & booking data  | Room details, check-in/check-out dates, duration | Owner, Caretaker                  |
| Expense records      | Category, amount, description                    | Owner only                        |
| Financial statements | Revenue, expenses, profit summaries              | Owner only                        |
| Activity logs        | Who did what and when                            | Owner (all), Caretaker (own only) |

### Data Access Principles

- Data is accessible only to **authenticated users** — no public endpoints expose personal or financial data
- Role-based restrictions limit sensitive financial data to **owners only** (see Section 4)
- Caretakers can only view their **own activity logs**, not those of other users

### Session Security

- Sessions are stored in the **database** (not files or cookies)
- Session data is **encrypted at rest** (`SESSION_ENCRYPT=true`)
- Session lifetime is **120 minutes**, after which re-authentication is required

### Transmission Security

- All data in transit is protected via **HTTPS/TLS** when deployed to production
- HTTPS is enforced programmatically in `AppServiceProvider`:
    ```php
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
    ```

### Sensitive Configuration

- All credentials (database passwords, app keys) are stored in the `.env` file
- The `.env` file is excluded from version control via `.gitignore`
- No credentials are hardcoded anywhere in the application source code

---

## 4. Access Control Policy

### Roles

The system has two user roles:

| Role          | Description                                                                          |
| ------------- | ------------------------------------------------------------------------------------ |
| **Owner**     | Full administrator access to all system features                                     |
| **Caretaker** | Day-to-day operational access; restricted from financial and administrative features |

### Permission Matrix

| Feature / Module              | Owner | Caretaker     |
| ----------------------------- | ----- | ------------- |
| Dashboard                     | ✅    | ✅            |
| Bookings (view, create, edit) | ✅    | ✅            |
| Bookings (delete)             | ✅    | ✅            |
| Tenants (view, create, edit)  | ✅    | ✅            |
| Invoices & Payments           | ✅    | ✅            |
| Rooms (view)                  | ✅    | ✅            |
| Rooms (create, edit, delete)  | ✅    | ❌ (403)      |
| Rates (create, edit, delete)  | ✅    | ❌ (403)      |
| Asset Inventory               | ✅    | ✅            |
| Electric Readings             | ✅    | ✅            |
| Maintenance Logs              | ✅    | ✅            |
| Security Deposits             | ✅    | ✅            |
| **Expenses** (all actions)    | ✅    | ❌ (403)      |
| **Settings**                  | ✅    | ❌ (403)      |
| **Financial Statement**       | ✅    | ❌ (403)      |
| **Sales Reports & Exports**   | ✅    | ❌ (403)      |
| **User Management**           | ✅    | ❌ (403)      |
| Activity Logs (all users)     | ✅    | ❌ (own only) |

### Implementation

Role enforcement is implemented via the `ChecksRole` trait (`app/Traits/ChecksRole.php`):

```php
protected function requireOwner(): void
{
    if (!$this->isOwner()) {
        abort(403, 'Unauthorized. Only admins can access this resource.');
    }
}
```

`$this->requireOwner()` is called at the start of every owner-only controller method. A `403 Forbidden` response is returned immediately if the logged-in user is not an owner.

### Account Management

- New user accounts are created **exclusively by the Owner** via the User Management module
- Public registration is **disabled** — the `/register` route does not exist
- Accounts can be **archived** (deactivated) by the Owner; archived accounts cannot log in

---

## 5. Logging & Monitoring Policy

### Activity Logging

Every significant user action is recorded automatically via the `LogsActivity` trait (`app/Traits/LogsActivity.php`). Each log entry captures:

| Field         | Description                                         |
| ------------- | --------------------------------------------------- |
| `user_id`     | Who performed the action                            |
| `action`      | Category of action (e.g., "Created Booking")        |
| `description` | Human-readable summary of what changed              |
| `model_type`  | The affected data type (e.g., "Booking", "Invoice") |
| `model_id`    | The ID of the affected record                       |
| `created_at`  | Exact timestamp of the action                       |

### Actions Logged

The following actions are captured across the system:

- Bookings: created, updated, deleted, check-in, check-out, renewed
- Tenants: created, updated, archived, activated
- Rooms: created, updated
- Rates: created, updated, deleted
- Invoices: penalty applied, bulk penalties applied
- Payments: collected
- Security Deposits: deduction applied, refunded, forfeited, rolled over, topped up
- Expenses: created, updated, deleted
- Electric Readings: recorded
- Assets: created, updated, assigned, moved
- Maintenance Logs: created, updated
- Settings: updated
- Users: created, updated, archived, activated

### Log Access Policy

- **Owners** can view all activity logs from all users, filterable by user, action type, and date range
- **Caretakers** can only view their own activity logs

### Log Review

Activity logs should be reviewed by the Owner at least **once a week** to detect unusual patterns such as:

- Bulk deletions
- Repeated access to sensitive records
- Actions performed outside of normal operating hours

---

## 6. Backup & Recovery Policy

### Backup Strategy

| Item                        | Frequency            | Method                                       |
| --------------------------- | -------------------- | -------------------------------------------- |
| Database (SQL Server)       | Daily                | SQL Server backup via SSMS or automated task |
| Application files           | On every code change | Version control (Git)                        |
| Environment config (`.env`) | On every change      | Stored securely offline (NOT in Git)         |

### Backup Retention

- Daily database backups: retained for **30 days**
- Weekly snapshots: retained for **3 months**

### Recovery Procedure

1. **Database restore:** Restore the latest `.bak` file via SQL Server Management Studio (SSMS) using `RESTORE DATABASE`
2. **Application restore:** Pull the latest commit from the Git repository (`git pull`)
3. **Config restore:** Replace `.env` with the secure offline copy and run `php artisan config:clear`
4. **Session reset:** Run `php artisan session:table` and `php artisan migrate` if session table is affected
5. **Verify:** Test login, dashboard, and core features before resuming normal operations

### Recovery Time Objective (RTO)

Target system restoration within **2 hours** of a confirmed data loss event.

---

## 7. Incident Response Plan

An incident is any event that compromises, or threatens to compromise, the confidentiality, integrity, or availability of the system or its data.

### Phase 1 — Detection

| Signal                                 | What to Check                        |
| -------------------------------------- | ------------------------------------ |
| Unexpected 403 errors in logs          | Possible unauthorized access attempt |
| Repeated failed logins (429 responses) | Possible brute-force attack          |
| Unusual activity log entries           | Unauthorized data modification       |
| System unavailable / errors            | Possible attack or server failure    |

**Tools:**

- Laravel activity logs (via Activity Logs module in the system)
- Laravel application log file: `storage/logs/laravel.log`
- SQL Server audit logs (if enabled)

---

### Phase 2 — Reporting

Once an incident is detected, report it immediately:

| Who                                | Contact Method                             |
| ---------------------------------- | ------------------------------------------ |
| System Owner                       | Direct notification (in-person or message) |
| Affected Tenants (if data exposed) | Written notification within 72 hours       |

**Report must include:**

- Date and time the incident was discovered
- Description of what was observed
- Systems and data potentially affected
- Who discovered it and how

---

### Phase 3 — Containment

Immediate steps to stop the threat from spreading:

1. **Disable compromised accounts** — archive the affected user account via User Management
2. **Revoke active sessions** — clear the `sessions` database table: `php artisan session:flush` (or truncate via SSMS)
3. **Take the system offline** — enable Laravel maintenance mode: `php artisan down`
4. **Change credentials** — rotate `APP_KEY`, database passwords, and all `.env` secrets
5. **Preserve evidence** — copy `storage/logs/laravel.log` before clearing or overwriting

---

### Phase 4 — Recovery

Steps to restore normal operations securely:

1. **Identify the root cause** — review activity logs and Laravel error logs for the attack vector
2. **Patch the vulnerability** — apply the fix before bringing the system back online
3. **Restore data if needed** — follow the Backup Recovery Procedure (Section 6)
4. **Re-generate application key** — run `php artisan key:generate` to invalidate existing encrypted data
5. **Bring system back online** — `php artisan up`
6. **Notify users** — inform the owner and relevant staff that the system is restored
7. **Post-incident review** — document what happened, what was done, and what changes are needed to prevent recurrence

---

_Last updated: February 26, 2026_
