# Security Policies & Documentation

## Sanasa Dormitory Management System

**Version:** 2.0  
**Date:** March 4, 2026  
**Framework:** Laravel 12 (PHP 8.2)  
**Database:** Microsoft SQL Server

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Secure Coding Practices](#2-secure-coding-practices)
3. [Authentication and Authorization](#3-authentication-and-authorization)
4. [Data Encryption](#4-data-encryption)
5. [Input Validation and Sanitization](#5-input-validation-and-sanitization)
6. [Error Handling and Logging](#6-error-handling-and-logging)
7. [Access Control](#7-access-control)
8. [Code Auditing Tools](#8-code-auditing-tools)
9. [Testing](#9-testing)
10. [Security Policies](#10-security-policies)
11. [Incident Response Plan](#11-incident-response-plan)

---

## 1. Project Overview

### System Description

The **Sanasa Dormitory Management System** is a web-based application for managing all aspects of a dormitory business, including tenant registration, room bookings, invoicing, payment collection, electric meter readings, asset inventory, maintenance tracking, expense management, security deposits, and financial reporting.

### Purpose of the System

The system aims to:

- **Digitize dormitory operations** — replacing manual paper-based processes with an efficient, auditable web application
- **Provide secure data management and access control** — protecting tenant personal information and financial records
- **Protect user accounts and system resources** — via multi-factor authentication, role-based access, and encrypted storage
- **Prevent common cyber threats** such as SQL injection, unauthorized access, brute-force attacks, and credential leakage
- **Generate financial reports** — revenue, expenses, and profit summaries for the dormitory owner

### Intended Users

The system is designed for:

| User Role     | Description                                                                                             |
| ------------- | ------------------------------------------------------------------------------------------------------- |
| **Owner**     | Full administrator responsible for managing the dormitory, users, finances, settings, and configuration |
| **Caretaker** | Day-to-day operational staff handling tenant check-ins, bookings, payments, and basic record keeping    |

There are **no guest or public users** — the system has no public registration. All accounts are created exclusively by the Owner.

### Platform and Technology Used

| Component                | Technology                                                 |
| ------------------------ | ---------------------------------------------------------- |
| **Programming Language** | PHP 8.2                                                    |
| **Framework**            | Laravel 12                                                 |
| **Database**             | Microsoft SQL Server                                       |
| **Platform**             | Web Application                                            |
| **Frontend**             | Blade Templates, Bootstrap 5.3, Bootstrap Icons            |
| **Build Tools**          | Vite (asset bundling)                                      |
| **Authentication**       | Laravel built-in + TOTP 2FA (`pragmarx/google2fa-laravel`) |
| **PDF Generation**       | `barryvdh/laravel-dompdf`                                  |
| **Excel Export**         | `maatwebsite/excel`                                        |
| **QR Code (2FA)**        | `bacon/bacon-qr-code`                                      |
| **Static Analysis**      | Larastan (PHPStan) level 5                                 |

---

## 2. Secure Coding Practices

### Avoiding Hardcoded Credentials

All sensitive credentials are stored in the `.env` environment file and loaded via Laravel's `env()` helper — **never hardcoded** in source code. The `.env` file is excluded from version control via `.gitignore`.

### Database Credentials

`config/database.php` loads all database connection details from environment variables:

```php
'sqlsrv' => [
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => env('DB_PORT', '1433'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
],
```

### Mail Credentials

`config/mail.php` loads SMTP credentials from environment variables:

```php
'smtp' => [
    'host'       => env('MAIL_HOST', '127.0.0.1'),
    'port'       => env('MAIL_PORT', 587),
    'username'   => env('MAIL_USERNAME'),
    'password'   => env('MAIL_PASSWORD'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
],
```

### reCAPTCHA Keys

`config/services.php` loads reCAPTCHA site and secret keys from environment variables:

```php
'recaptcha' => [
    'site_key'   => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
],
```

### Environment File Template

The `.env.example` file provides empty placeholders for all secrets, so developers know which variables are needed without exposing actual credentials:

```dotenv
DB_PASSWORD=
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
MAIL_USERNAME=
MAIL_PASSWORD=
```

### CSRF Protection

Every form includes a `@csrf` directive, and Laravel's `VerifyCsrfToken` middleware automatically rejects requests without a valid CSRF token:

```blade
<form action="{{ route('login') }}" method="POST">
    @csrf
    {{-- form fields --}}
</form>
```

### HTTPS Enforcement

In production, all URLs are forced to use HTTPS via `AppServiceProvider`:

```php
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```

_(screenshot: `.env.example` showing empty credential placeholders)_

_(screenshot: `.gitignore` showing `.env` is excluded from version control)_

_(screenshot: `config/database.php` showing credentials loaded from `env()`)_

---

## 3. Authentication and Authorization

### Login Process

The login flow uses a multi-step process with several security layers:

1. **Login Form** — User enters email and password, and completes a **Google reCAPTCHA v2** checkbox challenge
2. **Server-side reCAPTCHA verification** — The response is verified against Google's `siteverify` API
3. **Credential check** — `Auth::attempt()` verifies email/password against the database
4. **Account status check** — Archived (deactivated) accounts are blocked immediately
5. **2FA challenge** — If 2FA is enabled, the user is redirected to a 2FA verification page to enter a 6-digit TOTP code
6. **2FA setup enforcement** — If 2FA is not yet set up, the user is redirected to the 2FA setup page (mandatory)
7. **Session regeneration** — On successful login, the session ID is regenerated to prevent session fixation
8. **Dashboard access** — User is redirected to `/dashboard`

**reCAPTCHA server-side verification** in `LoginController.php`:

```php
$recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
    'secret'   => config('services.recaptcha.secret_key'),
    'response' => $request->input('g-recaptcha-response'),
    'remoteip' => $request->ip(),
]);

if (!$recaptchaResponse->json('success')) {
    throw ValidationException::withMessages([
        'g-recaptcha-response' => ['CAPTCHA verification failed.'],
    ]);
}
```

_(screenshot: Login page with email, password, and reCAPTCHA checkbox)_

_(screenshot: 2FA challenge page prompting for 6-digit TOTP code)_

### Password Hashing

Passwords are **never stored in plain text**. All passwords are hashed using **bcrypt** with **12 rounds** (`BCRYPT_ROUNDS=12`).

The `User` model casts the `password` field as `hashed`, ensuring automatic hashing on assignment:

```php
protected function casts(): array
{
    return [
        'password' => 'hashed',
    ];
}
```

Password and `remember_token` are excluded from serialization to prevent accidental exposure:

```php
protected $hidden = ['password', 'remember_token'];
```

### Multi-Factor Authentication (MFA / 2FA)

The system supports **TOTP-based two-factor authentication**, compatible with Google Authenticator, Authy, and Microsoft Authenticator.

**2FA is mandatory for all users.** New users are redirected to the 2FA setup page on first login and cannot access any part of the system until they enable it. This is enforced by the `Ensure2FAIsSetup` middleware.

**How it works:**

1. Users set up 2FA by scanning a QR code at `/two-factor-setup`
2. On subsequent logins, after entering email and password, users are redirected to `/two-factor-challenge`
3. A valid 6-digit TOTP code from their authenticator app is required to complete login
4. 2FA events are logged in the activity log

**Implementation:** `pragmarx/google2fa-laravel` package with AES-256-encrypted secrets stored in the `two_factor_secret` column (via Laravel's `encrypted` cast).

_(screenshot: 2FA setup page with QR code and verification field)_

_(screenshot: Mandatory 2FA warning banner for new users)_

### Password Reset via Email

If a user forgets their password, they can request a reset link sent to their registered email address.

**How it works:**

1. User clicks "Forgot your password?" on the login page
2. Enters their registered email address
3. System sends a time-limited reset link (expires in 60 minutes)
4. User clicks the link and sets a new password (must meet full password policy)
5. Password reset is logged in the activity log
6. Requests are throttled to 1 per 60 seconds to prevent abuse

**Implementation:** Laravel's built-in `PasswordBroker` with `password_reset_tokens` table.

_(screenshot: Forgot password page)_

_(screenshot: Password reset email received)_

### User Roles and Access Control

Two roles are implemented:

| Role          | Description                                                         |
| ------------- | ------------------------------------------------------------------- |
| **Owner**     | Full administrator access to all system features                    |
| **Caretaker** | Day-to-day operations; restricted from financial and admin features |

Access to system functions is restricted based on user roles (see Section 7 for the full permission matrix).

### AFK Auto-Logout

After **25 minutes** of inactivity, a warning modal appears ("Are you still there?"). If the user does not respond within **60 seconds**, they are automatically logged out. This prevents unauthorized access on unattended workstations.

Events tracked: mouse movement, keyboard input, mouse clicks, touch input, and scrolling.

_(screenshot: AFK auto-logout warning modal with countdown)_

---

## 4. Data Encryption

### Encrypted Data

The system encrypts sensitive data both **at rest** and **in transit**.

### Field-Level Encryption (AES-256)

Sensitive tenant personal data is encrypted at the database level using **AES-256-CBC** (Laravel's built-in `encrypted` cast, keyed by `APP_KEY`).

The following `tenants` table fields are encrypted before storage and decrypted transparently on read:

| Field               | Type of Data             |
| ------------------- | ------------------------ |
| `address`           | Home address             |
| `contact_num`       | Primary contact number   |
| `emer_contact_num`  | Emergency contact number |
| `emer_contact_name` | Emergency contact name   |
| `email`             | Email address            |
| `id_document`       | Government ID reference  |

Implementation in `app/Models/Tenant.php`:

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

### 2FA Secret Encryption

The `two_factor_secret` column in the `users` table is also encrypted using the same AES-256 mechanism:

```php
'two_factor_secret' => 'encrypted',
```

### Session Encryption

- Sessions are stored in the **database** (not files or cookies)
- Session data is **encrypted at rest** (`SESSION_ENCRYPT=true`)
- Session lifetime: **120 minutes**

### Transmission Security (HTTPS/TLS)

All data in transit is protected via **HTTPS/TLS** when deployed to production. HTTPS is enforced programmatically:

```php
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```

_(screenshot: SQL Server Management Studio showing encrypted ciphertext in the tenants table)_

_(screenshot: Application UI showing the same tenant data decrypted and readable)_

---

## 5. Input Validation and Sanitization

### Validated Inputs

**Every user input** in the system is validated server-side using Laravel's built-in validation framework. The system validates:

- **Login credentials** — email format, password, reCAPTCHA response
- **Form inputs** — all create/update forms across every module
- **File uploads** — image type, file extension, and file size
- **Search queries** — sort columns and directions are allowlisted
- **2FA codes** — must be exactly 6 digits
- **Password fields** — 12+ characters, mixed case, symbols, HIBP breach check

### Validation Rules Summary

| Module                | Validated Fields                                                                                                                                                       |
| --------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Login**             | `email` (required, email format), `password` (required), `g-recaptcha-response` (required)                                                                             |
| **User Management**   | `first_name`, `last_name` (required, max 255), `email` (unique), `password` (Password::defaults), `role` (in: owner, caretaker)                                        |
| **Tenants**           | `first_name`, `birth_date` (before 12 years ago), `id_document` (image, mimes, max 5MB), `contact_num`, `emer_contact_num`, `status` (in: active, inactive)            |
| **Bookings**          | `room_id` (exists), `tenant_ids` (array, min 1, distinct, each exists), `rate_id` (exists), `checkin_date` (date, after_or_equal today), `stay_length` (integer, 1–30) |
| **Rooms**             | `room_num` (unique), `floor`, `capacity` (integer), `status` (in: available, pending, occupied, maintenance, cleaning)                                                 |
| **Rates**             | `duration_type` (in: Daily, Weekly, Monthly), `base_price` (numeric, min 0), `utilities` (array with name + price)                                                     |
| **Payments**          | `booking_id` (exists), `invoice_id` (exists), `amount` (numeric, min 0.01), `payment_method` (in: Cash, GCash), `date_received` (date)                                 |
| **Refunds**           | `payment_id` (exists), `refund_amount` (numeric, min 0.01), `refund_method` (in: Cash, GCash), `reference_number` (required if GCash)                                  |
| **Security Deposits** | `amount` (min 0.01, max refundable), `category` (in: allowed categories), `refund_method` (in: Cash, Bank Transfer, GCash, Other)                                      |
| **Settings**          | `late_penalty_rate` (numeric, 0–100), `late_penalty_type` (in: percentage, fixed), `grace_days` (integer, 0–60), `invoice_due_days` (integer, 1–60)                    |
| **Expenses**          | `category` (max 100), `amount` (numeric, min 0.01), `expense_date` (date), `receipt_number` (max 100)                                                                  |
| **Electric Readings** | `room_id` (exists), `reading_date` (date), `meter_value_kwh` (numeric, min 0)                                                                                          |
| **Assets**            | `name` (max 255), `condition` (in: Good, Needs Repair, Broken, Missing), `room_id` (exists), `date_acquired` (date)                                                    |
| **Maintenance Logs**  | `description` (max 1000), `status` (in: Pending, In Progress, Completed, Cancelled), `date_reported` (date)                                                            |
| **Password Reset**    | `token` (required), `email` (email), `password` (min 12, mixed case, symbols, uncompromised)                                                                           |
| **2FA Codes**         | `code` (required, digits:6)                                                                                                                                            |

### File Upload Validation

File uploads are restricted to images only (tenant ID documents):

```php
'id_document' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
```

- **Allowed types:** JPEG, PNG, JPG, GIF
- **Maximum size:** 5 MB (5120 KB)
- Files are stored with unique names to prevent filename conflicts:

```php
$filename = 'id_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
$image->move(public_path('uploads/id_documents'), $filename);
```

### Search Query Protection

Search inputs use parameterized LIKE queries (Eloquent ORM). Sort columns and directions are **allowlisted** to prevent SQL injection via order-by manipulation:

```php
$allowedSortColumns = ['user_id', 'first_name', 'last_name', 'email', 'role', 'status', 'created_at'];
if (in_array($sortBy, $allowedSortColumns, true)) {
    $query->orderBy($sortBy, $sortDir);
}

if (!in_array($sortDir, ['asc', 'desc'], true)) {
    $sortDir = 'asc';
}

if (!in_array($perPage, [5, 10, 15, 20], true)) {
    $perPage = 25;
}
```

This allowlisting pattern is applied consistently across all controllers: `UserController`, `TenantController`, `BookingController`, `InvoiceController`, `AssetController`, `SecurityDepositController`, `MaintenanceLogController`, and `ExpenseController`.

### Sanitization Tools and Techniques

| Technique                                | Description                                                                                        |
| ---------------------------------------- | -------------------------------------------------------------------------------------------------- |
| **Blade `{{ }}` auto-escaping**          | All output rendered via `{{ }}` is automatically escaped with `htmlspecialchars()`, preventing XSS |
| **Eloquent ORM (parameterized queries)** | All database queries use parameter binding, preventing SQL injection                               |
| **Laravel validation framework**         | Server-side type checking, length limits, enumeration (`in:`), and existence rules (`exists:`)     |
| **Password policy (HIBP)**               | Passwords are checked against the Have I Been Pwned database via `uncompromised()` rule            |

_(screenshot: Validation error messages shown when submitting a form with invalid data)_

_(screenshot: File upload rejection when uploading a non-image file or file exceeding 5 MB)_

---

## 6. Error Handling and Logging

### What is it?

The secure management of system errors (to prevent technical leakage) alongside an automated audit trail that records all significant user actions.

### Custom Error Pages

`APP_DEBUG` is set to `false` in production. We created custom HTTP error pages (`403.blade.php`, `404.blade.php`, `500.blade.php`) to ensure stack traces, server paths, and framework versions are completely hidden from attackers.

| Error Code | Description           | File                                   |
| ---------- | --------------------- | -------------------------------------- |
| **403**    | Forbidden             | `resources/views/errors/403.blade.php` |
| **404**    | Not Found             | `resources/views/errors/404.blade.php` |
| **500**    | Internal Server Error | `resources/views/errors/500.blade.php` |

### Activity Logging

Every time a user modifies data or authenticates, the system automatically records the `user_id`, `action`, `description`, `model_type`, and `timestamp`.

| Field         | Description                                         |
| ------------- | --------------------------------------------------- |
| `user_id`     | Who performed the action                            |
| `action`      | Category of action (e.g., "Created Booking")        |
| `description` | Human-readable summary of what changed              |
| `model_type`  | The affected data type (e.g., "Booking", "Invoice") |
| `model_id`    | The ID of the affected record                       |
| `created_at`  | Exact timestamp of the action                       |

#### Logged Events

- **Authentication events:** Login Success, Login Success (2FA), Login Failed, Login Blocked, Account Locked, Logout
- **Account events:** Password Changed, 2FA Enabled, 2FA Verified
- **Booking events:** Created, Updated, Deleted, Check-in, Check-out, Renewed
- **Tenant events:** Created, Updated, Archived, Activated
- **Room events:** Created, Updated, Deleted
- **Rate events:** Created, Updated, Deleted
- **Invoice events:** Penalty Applied, Bulk Penalties Applied
- **Payment events:** Collected
- **Security Deposit events:** Deduction Applied, Refunded, Forfeited, Rolled Over, Topped Up
- **Expense events:** Created, Updated, Deleted
- **Electric Readings:** Recorded
- **Asset events:** Created, Updated, Assigned, Moved
- **Maintenance Log events:** Created, Updated
- **Settings events:** Updated
- **User events:** Created, Updated, Archived, Activated

#### Log Visual Indicators

Activity log entries are **color-coded** in the UI for quick identification:

| Badge Color | Actions                                                       |
| ----------- | ------------------------------------------------------------- |
| **Green**   | Login Success, Created records                                |
| **Red**     | Login Failed, Login Blocked, Account Locked, Deleted/Archived |
| **Amber**   | Login 2FA Required, Checked-in/out                            |
| **Blue**    | Updated records                                               |
| **Indigo**  | Payments, Generated records                                   |
| **Gray**    | Logout                                                        |

#### Log Access Policy

- **Owners** can view all activity logs from all users, filterable by user, action type, and date range
- **Caretakers** can only view their own activity logs

### Where in the code

The `LogsActivity` trait — `app/Traits/LogsActivity.php`:

```php
protected function logActivity(string $action, string $description, $model = null): void
{
    ActivityLog::create([
        'user_id'     => Auth::user()->user_id,
        'action'      => $action,
        'description' => $description,
        'model_type'  => $model ? get_class($model) : null,
        'model_id'    => $model ? $model->getKey() : null,
    ]);
}
```

### How to explain it

> "To ensure accountability, we developed an Activity Logging trait that records all CRUD operations and authentication events. We use color-coded log badges so admins can instantly spot red flags like failed login attempts. Furthermore, we implemented custom 400 and 500-level error pages to ensure that if a system crash occurs, no raw stack traces or internal server data are leaked to the browser."

_(screenshot: Custom 403 Forbidden error page)_

_(screenshot: Activity Logs page showing color-coded badges for different action types)_

---

## 7. Access Control

### Protected Pages and Resources

All pages in the system are behind authentication — there are **no public-facing pages** except the login and password reset forms. Every authenticated route is additionally gated by the `2fa.setup` middleware, ensuring 2FA is set up before any access.

### Role-Based Access Control (RBAC)

| System Feature / Resource               | Guest | Caretaker     | Owner (Admin) |
| --------------------------------------- | ----- | ------------- | ------------- |
| View Login Page                         | ✅    | N/A           | N/A           |
| Forgot Password                         | ✅    | N/A           | N/A           |
| Dashboard                               | ❌    | ✅            | ✅            |
| Bookings (view, create, edit, delete)   | ❌    | ✅            | ✅            |
| Tenants (view, create, edit)            | ❌    | ✅            | ✅            |
| Invoices & Payments                     | ❌    | ✅            | ✅            |
| Rooms (view)                            | ❌    | ✅            | ✅            |
| Rooms (create, edit, delete)            | ❌    | ❌ (403)      | ✅            |
| Rates (create, edit, delete)            | ❌    | ❌ (403)      | ✅            |
| Asset Inventory                         | ❌    | ✅            | ✅            |
| Electric Readings                       | ❌    | ✅            | ✅            |
| Maintenance Logs                        | ❌    | ✅            | ✅            |
| Security Deposits                       | ❌    | ✅            | ✅            |
| Expenses (all actions)                  | ❌    | ❌ (403)      | ✅            |
| Settings / Configuration                | ❌    | ❌ (403)      | ✅            |
| Financial Statement                     | ❌    | ❌ (403)      | ✅            |
| Sales Reports & Exports                 | ❌    | ❌ (403)      | ✅            |
| User Management (create, edit, archive) | ❌    | ❌ (403)      | ✅            |
| Activity Logs (all users)               | ❌    | ❌ (own only) | ✅            |
| My Account / Change Password            | ❌    | ✅            | ✅            |
| 2FA Setup                               | ❌    | ✅            | ✅            |

### How Unauthorized Access Is Prevented

#### Server-Side Role Check

Role enforcement is implemented via the `ChecksRole` trait (`app/Traits/ChecksRole.php`):

```php
protected function requireOwner(): void
{
    if (!$this->isOwner()) {
        abort(403, 'Unauthorized. Only admins can access this resource.');
    }
}
```

`$this->requireOwner()` is called at the start of every owner-only controller method. A **403 Forbidden** response is returned immediately if the logged-in user is not an owner.

#### Session Validation

- All routes use the `auth` middleware, which checks for a valid authenticated session
- Sessions are stored in the database with encryption (`SESSION_ENCRYPT=true`)
- Session is regenerated on login and destroyed on logout
- AFK auto-logout removes sessions after 25 minutes of inactivity

#### UI-Level Enforcement (Defense in Depth)

Owner-only pages are **completely hidden** from the sidebar navigation for caretaker users:

```blade
@if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
    {{-- Owner-only menu items: Expenses, Reports, Financial Statement, User Management, Settings --}}
@endif
```

Even if the UI hiding were bypassed (e.g., by manually typing the URL), the server-side `requireOwner()` check would still return a **403 Forbidden** response.

#### Mandatory 2FA Enforcement

All authenticated routes are wrapped in the `2fa.setup` middleware. If a user has not enabled 2FA, they are redirected to `/two-factor-setup` and cannot access any other page:

```php
// Ensure2FAIsSetup middleware
if (!$request->user()->two_factor_enabled) {
    return redirect()->route('two-factor.setup');
}
```

_(screenshot: Caretaker seeing 403 Forbidden when trying to access an owner-only page)_

_(screenshot: Caretaker sidebar showing only accessible pages)_

---

## 8. Code Auditing Tools

### Tools Used

| Tool                   | Purpose                           | Configuration        |
| ---------------------- | --------------------------------- | -------------------- |
| **Larastan (PHPStan)** | PHP static analysis for Laravel   | `phpstan.neon`       |
| **Composer Audit**     | Dependency vulnerability scanning | Built-in to Composer |
| **Laravel Pint**       | PHP code style enforcement        | Built-in to Laravel  |

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

**Level 5** checks include: unknown classes, unknown functions, unknown methods, wrong argument types, return type checks, and assignment type compatibility.

_(screenshot: Terminal output of `vendor/bin/phpstan analyse` showing analysis results)_

### Dependency Vulnerability Auditing

`composer audit` scans all Composer dependencies for known security vulnerabilities.

Run with: `composer audit`

Current status: **No known vulnerabilities found.**

_(screenshot: Terminal output of `composer audit` showing "No security vulnerability advisories found")_

### Vulnerabilities Detected and Fixes Applied

| Issue Category                 | Source     | Fix Applied                                                      |
| ------------------------------ | ---------- | ---------------------------------------------------------------- |
| Hardcoded credentials          | Code audit | Moved all secrets to `.env` environment variables                |
| Missing input validation       | Code audit | Added `$request->validate()` to all controller methods           |
| Unencrypted sensitive fields   | Code audit | Applied `encrypted` cast to 6 tenant PII fields + 2FA secret     |
| Missing 2FA enforcement        | Code audit | Created `Ensure2FAIsSetup` middleware, made 2FA mandatory        |
| Missing brute-force protection | Code audit | Added login rate limiting (5 attempts/min) + progressive lockout |
| Missing CAPTCHA                | Code audit | Added Google reCAPTCHA v2 to login form                          |
| Default error pages            | Code audit | Created custom 403, 404, 500 error pages                         |
| Weak password policy           | Code audit | Enforced 12+ chars, mixed case, symbols, HIBP breach check       |

---

## 9. Testing

### Tests Conducted

#### Authentication Testing

| Test Case                               | Expected Result                              | Status |
| --------------------------------------- | -------------------------------------------- | ------ |
| Login with valid credentials            | Redirect to dashboard (or 2FA challenge)     | ✅     |
| Login with invalid email                | "Invalid credentials" error message          | ✅     |
| Login with invalid password             | "Invalid credentials" error message          | ✅     |
| Login without reCAPTCHA                 | "CAPTCHA verification failed" error          | ✅     |
| Login with archived account             | "Account Locked" error message               | ✅     |
| 5 failed login attempts                 | "Too many attempts" lockout (5 minutes)      | ✅     |
| 10 failed login attempts                | Account permanently locked (archived)        | ✅     |
| 2FA setup required for new user         | Redirected to 2FA setup page                 | ✅     |
| 2FA challenge with valid code           | Login successful, redirect to dashboard      | ✅     |
| 2FA challenge with invalid code         | "Invalid code" error                         | ✅     |
| Forgot password with valid email        | Reset link email sent, success message shown | ✅     |
| Forgot password with unknown email      | Generic message (no email enumeration)       | ✅     |
| Reset password with valid token         | Password updated, redirect to login          | ✅     |
| Reset password with expired token       | "Invalid or expired token" error             | ✅     |
| AFK auto-logout after 25 min inactivity | Warning modal appears, then auto-logout      | ✅     |
| Logout                                  | Session destroyed, redirect to login         | ✅     |

#### Input Validation Testing

| Test Case                              | Expected Result                                | Status |
| -------------------------------------- | ---------------------------------------------- | ------ |
| Submit form with empty required fields | Validation errors displayed per field          | ✅     |
| Submit form with invalid email format  | "Must be a valid email" error                  | ✅     |
| Create user with duplicate email       | "Email already taken" error                    | ✅     |
| Upload non-image file as ID document   | "Must be an image (jpeg, png, jpg, gif)" error | ✅     |
| Upload file exceeding 5 MB             | "File too large" error                         | ✅     |
| Create password under 12 characters    | "At least 12 characters" error                 | ✅     |
| Create password without symbols        | "Must contain symbols" error                   | ✅     |
| Set booking check-in date in the past  | "Must be today or later" error                 | ✅     |
| Enter negative payment amount          | "Must be at least 0.01" error                  | ✅     |
| Enter invalid room status              | Validation rejection (not in allowed list)     | ✅     |

#### Access Control Testing

| Test Case                              | Expected Result          | Status |
| -------------------------------------- | ------------------------ | ------ |
| Caretaker accesses Expenses page       | 403 Forbidden            | ✅     |
| Caretaker accesses Settings page       | 403 Forbidden            | ✅     |
| Caretaker accesses User Management     | 403 Forbidden            | ✅     |
| Caretaker accesses Financial Statement | 403 Forbidden            | ✅     |
| Caretaker creates/edits Room           | 403 Forbidden            | ✅     |
| Caretaker creates/edits Rate           | 403 Forbidden            | ✅     |
| Caretaker views own activity logs      | Only own logs visible    | ✅     |
| Guest accesses dashboard URL directly  | Redirected to login page | ✅     |
| Owner accesses all features            | Full access granted      | ✅     |

#### Feature and Functionality Testing

| Test Case                          | Expected Result                               | Status |
| ---------------------------------- | --------------------------------------------- | ------ |
| Create and manage bookings         | Booking created, invoices generated           | ✅     |
| Record payments                    | Payment logged, invoice status updated        | ✅     |
| Create and edit tenants            | Tenant saved with encrypted PII fields        | ✅     |
| Record electric meter readings     | Reading saved, available for invoicing        | ✅     |
| Manage assets and maintenance logs | Records created and updated correctly         | ✅     |
| Generate PDF receipts/reports      | PDF downloaded successfully                   | ✅     |
| Export payment history to Excel    | Excel file downloaded successfully            | ✅     |
| Change password via My Account     | Password updated, activity logged             | ✅     |
| Archive and activate user accounts | Account status toggled, login blocked/allowed | ✅     |
| Update system settings             | Settings saved and applied to calculations    | ✅     |
| All sidebar links and navigation   | All links navigate to correct pages           | ✅     |
| All buttons and form submissions   | All functions execute as expected             | ✅     |

_(screenshot: Successful login flow with 2FA verification)_

_(screenshot: Validation errors shown on a form submission)_

_(screenshot: 403 Forbidden page when caretaker accesses restricted feature)_

_(screenshot: All dashboard features working correctly)_

---

## 10. Security Policies

### 10.1 Password Policy

All user passwords must meet the following requirements, enforced at registration, user creation, and password change:

| Requirement          | Rule                                                        |
| -------------------- | ----------------------------------------------------------- |
| Minimum length       | **12 characters**                                           |
| Must contain letters | At least one alphabetic character                           |
| Mixed case           | Must contain both uppercase and lowercase letters           |
| Must contain numbers | At least one numeric digit                                  |
| Must contain symbols | At least one special character (e.g. `!@#$%`)               |
| Not compromised      | Must not appear in known data breach databases (HIBP check) |
| No reuse             | Cannot set a new password identical to the current password |

Password policy is enforced globally via `Password::defaults()` in `AppServiceProvider.php`.

### 10.2 Login Attempt Policy

| Rule                         | Setting                                                   |
| ---------------------------- | --------------------------------------------------------- |
| Max failed attempts          | **5 per email+IP address**                                |
| First lockout duration       | **5 minutes**                                             |
| After 10 total failures      | Account **permanently locked** (status set to `archived`) |
| Rate limiting on login route | `throttle:5,1` middleware (5 requests per minute per IP)  |
| reCAPTCHA required           | Google reCAPTCHA v2 checkbox on every login attempt       |

### 10.3 Data Handling Policy

| Rule                       | Implementation                                             |
| -------------------------- | ---------------------------------------------------------- |
| Encryption at rest         | AES-256 encryption on 6 tenant PII fields + 2FA secrets    |
| Session encryption         | `SESSION_ENCRYPT=true`, database-stored sessions           |
| Transmission encryption    | HTTPS/TLS enforced in production via `URL::forceScheme`    |
| Authorized access only     | All data behind `auth` middleware; role-based restrictions |
| No public data exposure    | No public API endpoints; no public registration            |
| Sensitive config exclusion | `.env` excluded from version control via `.gitignore`      |

### 10.4 Access Control Policy

| Rule                         | Implementation                                             |
| ---------------------------- | ---------------------------------------------------------- |
| Admin-only configuration     | Settings, User Management, Expenses, Reports → Owner only  |
| Server-side role enforcement | `requireOwner()` via `ChecksRole` trait → 403 on violation |
| UI-level hiding              | Sidebar hides restricted items for caretakers              |
| Mandatory 2FA                | `Ensure2FAIsSetup` middleware blocks access without 2FA    |
| Account creation             | Only the Owner can create new user accounts                |
| Account deactivation         | Owner can archive accounts; archived accounts cannot login |

### 10.5 Logging and Monitoring Policy

| Rule                         | Implementation                                                                                  |
| ---------------------------- | ----------------------------------------------------------------------------------------------- |
| All actions logged           | `LogsActivity` trait used in every controller                                                   |
| Authentication events logged | Login success, failure, lockout, 2FA events, logout                                             |
| Log access restricted        | Owners see all logs; caretakers see only their own                                              |
| Regular review               | Owner should review logs at least **once a week**                                               |
| Color-coded badges           | Green (success), Red (failure), Amber (warning), Blue (update), Indigo (payment), Gray (logout) |

### 10.6 Backup and Recovery Policy

| Item                        | Frequency            | Method                                       |
| --------------------------- | -------------------- | -------------------------------------------- |
| Database (SQL Server)       | Daily / Weekly       | SQL Server backup via SSMS or automated task |
| Application files           | On every code change | Version control (Git)                        |
| Environment config (`.env`) | On every change      | Stored securely offline (NOT in Git)         |

**Retention:** Daily backups retained for 30 days; weekly snapshots retained for 3 months.

**Recovery Procedure:**

1. **Database restore:** Restore the latest `.bak` file via SSMS using `RESTORE DATABASE`
2. **Application restore:** Pull the latest commit from the Git repository
3. **Config restore:** Replace `.env` with the secure offline copy and run `php artisan config:clear`
4. **Session reset:** Reset the sessions table if affected
5. **Verify:** Test login, dashboard, and core features before resuming operations

**Recovery Time Objective (RTO):** Restore within **2 hours** of a confirmed data loss event.

---

## 11. Incident Response Plan

An incident is any event that compromises, or threatens to compromise, the confidentiality, integrity, or availability of the system or its data.

### Phase 1 — Detection

| Signal                                 | What to Check                        |
| -------------------------------------- | ------------------------------------ |
| Unexpected 403 errors in logs          | Possible unauthorized access attempt |
| Repeated failed logins (429 responses) | Possible brute-force attack          |
| Unusual activity log entries           | Unauthorized data modification       |
| System unavailable / errors            | Possible attack or server failure    |

**Detection tools:**

- Activity Logs module in the system (color-coded, filterable)
- Laravel application log: `storage/logs/laravel.log`
- SQL Server audit logs (if enabled)

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

### Phase 3 — Containment

Immediate steps to stop the threat from spreading:

1. **Disable compromised accounts** — archive the affected user account via User Management
2. **Revoke active sessions** — clear the `sessions` database table: `php artisan session:flush` (or truncate via SSMS)
3. **Take the system offline** — enable Laravel maintenance mode: `php artisan down`
4. **Change credentials** — rotate `APP_KEY`, database passwords, and all `.env` secrets
5. **Preserve evidence** — copy `storage/logs/laravel.log` before clearing or overwriting

### Phase 4 — Recovery

Steps to restore normal operations securely:

1. **Identify the root cause** — review activity logs and Laravel error logs for the attack vector
2. **Patch the vulnerability** — apply the fix before bringing the system back online
3. **Restore data if needed** — follow the Backup Recovery Procedure (Section 10.6)
4. **Re-generate application key** — run `php artisan key:generate` to invalidate existing encrypted data
5. **Bring system back online** — `php artisan up`
6. **Notify users** — inform the owner and relevant staff that the system is restored
7. **Post-incident review** — document what happened, what was done, and what changes are needed to prevent recurrence

---

_Last updated: March 4, 2026_
