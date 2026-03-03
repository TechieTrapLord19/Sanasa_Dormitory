# Security Policies & Documentation

## Sanasa Dormitory Management System

**Version:** 1.1  
**Date:** March 3, 2026  
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
8. [Error Handling](#8-error-handling)
9. [Code Auditing & Static Analysis](#9-code-auditing--static-analysis)

---

## 1. Password Policy

### Requirements

All user passwords must meet the following minimum requirements, enforced by the system at registration and password update:

| Requirement          | Rule                                                        |
| -------------------- | ----------------------------------------------------------- |
| Minimum length       | **12 characters**                                           |
| Must contain letters | At least one alphabetic character                           |
| Mixed case           | Must contain both uppercase and lowercase letters           |
| Must contain numbers | At least one numeric digit                                  |
| Must contain symbols | At least one special character (e.g. `!@#$%`)               |
| Not compromised      | Must not appear in known data breach databases (HIBP check) |
| Confirmation         | Password must be entered twice (confirmed match)            |

### Implementation

Password rules are enforced globally in `app/Providers/AppServiceProvider.php` using Laravel's `Password::defaults()`:

```php
Password::defaults(function () {
    return Password::min(12)
        ->mixedCase()
        ->letters()
        ->numbers()
        ->symbols()
        ->uncompromised();
});
```

All controllers that create or update user credentials call `Password::defaults()` in their validation rules, ensuring consistent enforcement across the application.

_(screenshot: Password validation error messages shown when creating a user with a weak password)_

### Storage

- Passwords are **never stored in plain text**
- All passwords are hashed using **bcrypt** with **12 rounds** (`BCRYPT_ROUNDS=12`)
- Laravel's `User` model casts the `password` field as `hashed`, adding an additional layer of security

### Multi-Factor Authentication (MFA / 2FA)

The system supports **TOTP-based two-factor authentication** (Time-based One-Time Password), compatible with:

- Google Authenticator
- Authy
- Microsoft Authenticator

**How it works:**

1. Users set up 2FA by scanning a QR code at `/two-factor-setup`
2. On subsequent logins, after entering email and password, users are redirected to a 2FA challenge page
3. A valid 6-digit TOTP code from their authenticator app is required to complete login
4. 2FA events are logged in the activity log

**Implementation:** `pragmarx/google2fa-laravel` package using AES-256-encrypted secrets stored in the `two_factor_secret` column of the `users` table (via Laravel's `encrypted` cast).

_(screenshot: 2FA setup page with QR code)_

_(screenshot: 2FA challenge page prompting for 6-digit code)_

---

## 2. Login Attempt Policy

### Rate Limiting

To prevent brute-force attacks, the login endpoint is protected by Laravel's built-in throttle middleware:

- **Maximum attempts:** 5 per minute per IP address
- **Lockout duration:** 1 minute (automatic, no manual unlock needed)

### Implementation

Applied directly on the POST `/login` route in `routes/web.php`:

```php
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
```

_(screenshot: "Too Many Attempts" error shown after 5 failed login attempts)_

### Additional Protections

- **Google reCAPTCHA v2** — The login form requires a reCAPTCHA checkbox challenge before submission; the response is verified server-side via Google's `siteverify` API. This prevents automated bot logins and credential-stuffing attacks.
- **Session regeneration** on every successful login (prevents session fixation attacks)
- **Session invalidation** on logout (old session token is destroyed)
- **CSRF token regeneration** on logout
- **Archived account check** — deactivated accounts are blocked immediately after authentication attempt
- **AFK Auto-Logout** — After 25 minutes of inactivity, a warning modal appears ("Are you still there?"). If the user does not respond within 60 seconds, they are automatically logged out. This prevents unauthorized access on unattended workstations.

_(screenshot: Login page with reCAPTCHA checkbox visible)_

_(screenshot: AFK auto-logout modal showing "Are you still there?" warning)_

_(screenshot: Login form validation — empty field highlighting with error messages)_

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

### Field-Level Data Encryption

Sensitive tenant personal data is encrypted at the database level using **AES-256** (Laravel's built-in `encrypted` cast, keyed by `APP_KEY`).

The following `tenants` table fields are encrypted before storage and decrypted transparently on read:

| Field               | Type of Data             |
| ------------------- | ------------------------ |
| `address`           | Home address             |
| `contact_num`       | Primary contact number   |
| `emer_contact_num`  | Emergency contact number |
| `emer_contact_name` | Emergency contact name   |
| `email`             | Email address            |
| `id_document`       | Government ID reference  |

Encryption is applied in `app/Models/Tenant.php` via model casts:

```php
protected $casts = [
    'address'           => 'encrypted',
    'contact_num'       => 'encrypted',
    'emer_contact_num'  => 'encrypted',
    'email'             => 'encrypted',
    'id_document'       => 'encrypted',
    'emer_contact_name' => 'encrypted',
];
```

Raw database values appear as AES-256 ciphertext (e.g., `eyJpdiI6...`), unreadable without the application key.

_(screenshot: SQL Server Management Studio showing encrypted ciphertext values in the tenants table)_

_(screenshot: Application UI showing the same tenant data decrypted and readable)_

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

_(screenshot: .env file showing sensitive values are stored here, not in code)_

_(screenshot: .gitignore file showing .env is excluded from version control)_

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

_(screenshot: Caretaker seeing 403 Forbidden page when trying to access an owner-only feature)_

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

### UI-Level Enforcement

In addition to server-side checks, owner-only pages are **completely hidden** from the sidebar navigation for caretaker users. The following sidebar items are only visible when the logged-in user's role is `owner`:

- Expenses
- Sales & Reports
- Financial Statement
- User Management
- Settings

This is implemented using Blade `@if` directives in `resources/views/layouts/app.blade.php`:

```blade
@if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
    {{-- Owner-only menu items --}}
@endif
```

This provides **defense in depth** — even if the UI hiding were bypassed (e.g., by manually typing the URL), the server-side `requireOwner()` check would still return a `403 Forbidden` response.

_(screenshot: Caretaker sidebar showing only accessible pages — no Expenses, Sales, Financial Statement, User Management, or Settings visible)_

### Account Management

- New user accounts are created **exclusively by the Owner** via the User Management module
- Public registration is **disabled** — the `/register` route does not exist
- Accounts can be **archived** (deactivated) by the Owner; archived accounts cannot log in

_(screenshot: User Management page showing owner and caretaker accounts with archive/activate buttons)_

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
- 2FA: enabled, verified

### Log Access Policy

- **Owners** can view all activity logs from all users, filterable by user, action type, and date range
- **Caretakers** can only view their own activity logs

### Log Review

Activity logs should be reviewed by the Owner at least **once a week** to detect unusual patterns such as:

- Bulk deletions
- Repeated access to sensitive records
- Actions performed outside of normal operating hours

_(screenshot: Activity Logs page showing logged actions with user, action type, description, and timestamps)_

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

## 8. Error Handling

### Custom Error Pages

The application uses custom-styled error pages for common HTTP error codes, preventing information leakage from default framework error pages:

| Error Code | Description           | File                                   |
| ---------- | --------------------- | -------------------------------------- |
| **403**    | Forbidden             | `resources/views/errors/403.blade.php` |
| **404**    | Not Found             | `resources/views/errors/404.blade.php` |
| **500**    | Internal Server Error | `resources/views/errors/500.blade.php` |

Each error page displays a user-friendly message without exposing stack traces, file paths, or server details, with a link back to the dashboard.

_(screenshot: Custom 403 Forbidden error page)_

_(screenshot: Custom 404 Not Found error page)_

### Application-Level Error Handling

- `APP_DEBUG=false` in production — disables detailed error messages
- All exceptions are logged to `storage/logs/laravel.log`
- Validation errors are returned as structured error bags, never raw exceptions

---

## 9. Code Auditing & Static Analysis

### Tools Used

| Tool                   | Purpose                           | Configuration File |
| ---------------------- | --------------------------------- | ------------------ |
| **Larastan (PHPStan)** | PHP static analysis for Laravel   | `phpstan.neon`     |
| **Composer Audit**     | Dependency vulnerability scanning | Built-in           |
| **Laravel Pint**       | PHP code style enforcement        | Built-in           |

### PHPStan / Larastan

The project uses **Larastan v3.x** (a PHPStan extension for Laravel) at **level 5** for static type analysis:

```yaml
# phpstan.neon
includes:
    - vendor/larastan/larastan/extension.neon
parameters:
    paths:
        - app/
    level: 5
```

Run with: `vendor/bin/phpstan analyse`

_(screenshot: Terminal output of `vendor/bin/phpstan analyse` showing static analysis results)_

### Dependency Auditing

`composer audit` is used to scan all Composer dependencies for known security vulnerabilities. Current status: **No known vulnerabilities found.**

_(screenshot: Terminal output of `composer audit` showing "No security vulnerability advisories found")_

---

_Last updated: March 3, 2026_
