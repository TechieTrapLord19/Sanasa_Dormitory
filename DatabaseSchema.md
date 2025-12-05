# Sanasa Dormitory Management System - Database Schema

## Overview

This document describes the database schema for the Sanasa Dormitory Management System. The system uses **Microsoft SQL Server** as the database engine.

---

## Tables

### 1. users

Stores user accounts for system access (owners and caretakers).

| Column            | Data Type                  | Constraints                   | Description                      |
| ----------------- | -------------------------- | ----------------------------- | -------------------------------- |
| user_id           | BIGINT                     | PRIMARY KEY, AUTO INCREMENT   | Unique identifier for each user  |
| first_name        | VARCHAR(255)               | NOT NULL                      | User's first name                |
| middle_name       | VARCHAR(255)               | NULLABLE                      | User's middle name               |
| last_name         | VARCHAR(255)               | NOT NULL                      | User's last name                 |
| email             | VARCHAR(255)               | NOT NULL, UNIQUE              | User's email address             |
| birth_date        | DATE                       | NULLABLE                      | User's birth date                |
| address           | TEXT                       | NULLABLE                      | User's address                   |
| role              | ENUM('owner', 'caretaker') | NOT NULL, DEFAULT 'caretaker' | User role in the system          |
| email_verified_at | TIMESTAMP                  | NULLABLE                      | Email verification timestamp     |
| password          | VARCHAR(255)               | NOT NULL                      | Hashed password                  |
| remember_token    | VARCHAR(100)               | NULLABLE                      | Token for "remember me" sessions |
| created_at        | TIMESTAMP                  | NULLABLE                      | Record creation timestamp        |
| updated_at        | TIMESTAMP                  | NULLABLE                      | Record update timestamp          |

---

### 2. rooms

Stores information about dormitory rooms.

| Column     | Data Type    | Constraints                                                 | Description                     |
| ---------- | ------------ | ----------------------------------------------------------- | ------------------------------- |
| room_id    | BIGINT       | PRIMARY KEY, AUTO INCREMENT                                 | Unique identifier for each room |
| room_num   | VARCHAR(255) | NOT NULL, UNIQUE                                            | Room number/name                |
| floor      | VARCHAR(255) | NOT NULL                                                    | Floor where the room is located |
| capacity   | INTEGER      | NOT NULL                                                    | Maximum occupancy               |
| status     | VARCHAR(255) | NOT NULL, CHECK (IN 'available', 'occupied', 'maintenance') | Current room status             |
| created_at | TIMESTAMP    | NULLABLE                                                    | Record creation timestamp       |
| updated_at | TIMESTAMP    | NULLABLE                                                    | Record update timestamp         |

**Constraints:**

-   `CHK_RoomStatus`: status IN ('available', 'occupied', 'maintenance')

---

### 3. rates

Stores pricing information for different stay durations.

| Column        | Data Type     | Constraints                 | Description                               |
| ------------- | ------------- | --------------------------- | ----------------------------------------- |
| rate_id       | BIGINT        | PRIMARY KEY, AUTO INCREMENT | Unique identifier for each rate           |
| rate_name     | VARCHAR(255)  | NULLABLE                    | Name/label for the rate                   |
| duration_type | VARCHAR(255)  | NOT NULL                    | Type of duration (Daily, Weekly, Monthly) |
| base_price    | DECIMAL(10,2) | NOT NULL                    | Base price for the rate                   |
| description   | TEXT          | NOT NULL                    | Description of what's included            |
| created_at    | TIMESTAMP     | NULLABLE                    | Record creation timestamp                 |
| updated_at    | TIMESTAMP     | NULLABLE                    | Record update timestamp                   |

---

### 4. utilities

Stores utility services and their prices associated with rates.

| Column       | Data Type     | Constraints                  | Description                        |
| ------------ | ------------- | ---------------------------- | ---------------------------------- |
| utilities_id | BIGINT        | PRIMARY KEY, AUTO INCREMENT  | Unique identifier for each utility |
| rate_id      | BIGINT        | FOREIGN KEY → rates(rate_id) | Associated rate                    |
| name         | VARCHAR(255)  | NOT NULL                     | Utility name (e.g., Water, Wi-Fi)  |
| price        | DECIMAL(10,2) | NOT NULL                     | Utility price                      |
| created_at   | TIMESTAMP     | NULLABLE                     | Record creation timestamp          |
| updated_at   | TIMESTAMP     | NULLABLE                     | Record update timestamp            |

**Foreign Keys:**

-   `rate_id` → `rates(rate_id)`

---

### 5. tenants

Stores tenant (lodger) information.

| Column           | Data Type                  | Constraints                 | Description                       |
| ---------------- | -------------------------- | --------------------------- | --------------------------------- |
| tenant_id        | BIGINT                     | PRIMARY KEY, AUTO INCREMENT | Unique identifier for each tenant |
| first_name       | VARCHAR(255)               | NOT NULL                    | Tenant's first name               |
| middle_name      | VARCHAR(255)               | NULLABLE                    | Tenant's middle name              |
| last_name        | VARCHAR(255)               | NOT NULL                    | Tenant's last name                |
| email            | VARCHAR(255)               | NULLABLE                    | Tenant's email address            |
| address          | TEXT                       | NULLABLE                    | Tenant's permanent address        |
| birth_date       | DATE                       | NULLABLE                    | Tenant's birth date               |
| id_document      | VARCHAR(255)               | NULLABLE                    | ID document reference/path        |
| contact_num      | VARCHAR(255)               | NULLABLE                    | Tenant's contact number           |
| emer_contact_num | VARCHAR(255)               | NULLABLE                    | Emergency contact number          |
| status           | ENUM('active', 'inactive') | NOT NULL, DEFAULT 'active'  | Tenant's active status            |
| created_at       | TIMESTAMP                  | NULLABLE                    | Record creation timestamp         |
| updated_at       | TIMESTAMP                  | NULLABLE                    | Record update timestamp           |

---

### 6. bookings

Stores room reservations and stays.

| Column               | Data Type     | Constraints                                | Description                                            |
| -------------------- | ------------- | ------------------------------------------ | ------------------------------------------------------ |
| booking_id           | BIGINT        | PRIMARY KEY, AUTO INCREMENT                | Unique identifier for each booking                     |
| room_id              | BIGINT        | FOREIGN KEY → rooms(room_id)               | Booked room                                            |
| tenant_id            | BIGINT        | FOREIGN KEY → tenants(tenant_id)           | Primary tenant                                         |
| secondary_tenant_id  | BIGINT        | FOREIGN KEY → tenants(tenant_id), NULLABLE | Secondary tenant (for shared rooms)                    |
| rate_id              | BIGINT        | FOREIGN KEY → rates(rate_id)               | Applied rate                                           |
| recorded_by_user_id  | BIGINT        | FOREIGN KEY → users(user_id)               | User who created the booking                           |
| checkin_date         | DATE          | NOT NULL                                   | Check-in date                                          |
| checkout_date        | DATE          | NOT NULL                                   | Check-out date                                         |
| total_calculated_fee | DECIMAL(10,2) | NOT NULL                                   | Total calculated fee for the stay                      |
| status               | VARCHAR(255)  | NOT NULL                                   | Booking status (Reserved, Active, Completed, Canceled) |
| cancellation_reason  | TEXT          | NULLABLE                                   | Reason for cancellation (if canceled)                  |
| created_at           | TIMESTAMP     | NULLABLE                                   | Record creation timestamp                              |
| updated_at           | TIMESTAMP     | NULLABLE                                   | Record update timestamp                                |

**Foreign Keys:**

-   `room_id` → `rooms(room_id)`
-   `tenant_id` → `tenants(tenant_id)`
-   `secondary_tenant_id` → `tenants(tenant_id)` ON DELETE SET NULL
-   `rate_id` → `rates(rate_id)`
-   `recorded_by_user_id` → `users(user_id)`

---

### 7. invoices

Stores billing invoices for bookings.

| Column                  | Data Type     | Constraints                        | Description                        |
| ----------------------- | ------------- | ---------------------------------- | ---------------------------------- |
| invoice_id              | BIGINT        | PRIMARY KEY, AUTO INCREMENT        | Unique identifier for each invoice |
| booking_id              | BIGINT        | FOREIGN KEY → bookings(booking_id) | Associated booking                 |
| date_generated          | DATE          | NOT NULL                           | Invoice generation date            |
| due_date                | DATE          | NULLABLE                           | Payment due date                   |
| rent_subtotal           | DECIMAL(10,2) | NOT NULL                           | Rent amount                        |
| utility_electricity_fee | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00             | Electricity fee                    |
| total_due               | DECIMAL(10,2) | NOT NULL                           | Total amount due                   |
| penalty_amount          | DECIMAL(10,2) | NOT NULL, DEFAULT 0.00             | Late payment penalty amount        |
| days_overdue            | INTEGER       | NOT NULL, DEFAULT 0                | Number of days overdue             |
| is_paid                 | BOOLEAN       | NOT NULL, DEFAULT FALSE            | Payment status flag                |
| created_at              | TIMESTAMP     | NULLABLE                           | Record creation timestamp          |
| updated_at              | TIMESTAMP     | NULLABLE                           | Record update timestamp            |

**Foreign Keys:**

-   `booking_id` → `bookings(booking_id)`

---

### 8. invoice_utilities

Stores individual utility charges for each invoice.

| Column             | Data Type     | Constraints                                          | Description                              |
| ------------------ | ------------- | ---------------------------------------------------- | ---------------------------------------- |
| invoice_utility_id | BIGINT        | PRIMARY KEY, AUTO INCREMENT                          | Unique identifier                        |
| invoice_id         | BIGINT        | FOREIGN KEY → invoices(invoice_id) ON DELETE CASCADE | Associated invoice                       |
| utility_name       | VARCHAR(255)  | NOT NULL                                             | Name of the utility (Water, Wi-Fi, etc.) |
| amount             | DECIMAL(10,2) | NOT NULL                                             | Utility charge amount                    |
| created_at         | TIMESTAMP     | NULLABLE                                             | Record creation timestamp                |
| updated_at         | TIMESTAMP     | NULLABLE                                             | Record update timestamp                  |

**Foreign Keys:**

-   `invoice_id` → `invoices(invoice_id)` ON DELETE CASCADE

---

### 9. payments

Stores payment transactions.

| Column               | Data Type     | Constraints                                  | Description                        |
| -------------------- | ------------- | -------------------------------------------- | ---------------------------------- |
| payment_id           | BIGINT        | PRIMARY KEY, AUTO INCREMENT                  | Unique identifier for each payment |
| booking_id           | BIGINT        | FOREIGN KEY → bookings(booking_id)           | Associated booking                 |
| invoice_id           | BIGINT        | FOREIGN KEY → invoices(invoice_id), NULLABLE | Associated invoice                 |
| collected_by_user_id | BIGINT        | FOREIGN KEY → users(user_id)                 | User who collected the payment     |
| payment_type         | VARCHAR(255)  | NOT NULL, CHECK                              | Type of payment                    |
| amount               | DECIMAL(10,2) | NOT NULL                                     | Payment amount                     |
| payment_method       | VARCHAR(255)  | NOT NULL                                     | Payment method (Cash, GCash)       |
| reference_number     | VARCHAR(255)  | NULLABLE                                     | Transaction reference (for GCash)  |
| date_received        | DATE          | NOT NULL                                     | Date payment was received          |
| created_at           | TIMESTAMP     | NULLABLE                                     | Record creation timestamp          |
| updated_at           | TIMESTAMP     | NULLABLE                                     | Record update timestamp            |

**Foreign Keys:**

-   `booking_id` → `bookings(booking_id)`
-   `invoice_id` → `invoices(invoice_id)`
-   `collected_by_user_id` → `users(user_id)`

**Constraints:**

-   `CHK_PaymentType`: payment_type IN ('Rent/Utility', 'Security Deposit', 'Deposit Deduction', 'Deposit Refund')

---

### 10. security_deposits

Stores security deposit information for monthly bookings.

| Column               | Data Type     | Constraints                                                     | Description                                                             |
| -------------------- | ------------- | --------------------------------------------------------------- | ----------------------------------------------------------------------- |
| security_deposit_id  | BIGINT        | PRIMARY KEY, AUTO INCREMENT                                     | Unique identifier                                                       |
| booking_id           | BIGINT        | FOREIGN KEY → bookings(booking_id) ON DELETE CASCADE            | Associated booking                                                      |
| invoice_id           | BIGINT        | FOREIGN KEY → invoices(invoice_id), NULLABLE ON DELETE SET NULL | Associated invoice                                                      |
| amount_required      | DECIMAL(10,2) | NOT NULL                                                        | Required deposit amount                                                 |
| amount_paid          | DECIMAL(10,2) | NOT NULL, DEFAULT 0                                             | Amount paid by tenant                                                   |
| amount_deducted      | DECIMAL(10,2) | NOT NULL, DEFAULT 0                                             | Total deductions applied                                                |
| amount_refunded      | DECIMAL(10,2) | NOT NULL, DEFAULT 0                                             | Amount refunded to tenant                                               |
| status               | VARCHAR(255)  | NOT NULL, DEFAULT 'Pending'                                     | Deposit status (Pending, Held, Partially Refunded, Refunded, Forfeited) |
| deduction_reason     | TEXT          | NULLABLE                                                        | Description of deductions                                               |
| notes                | TEXT          | NULLABLE                                                        | Additional notes                                                        |
| refunded_at          | DATETIME      | NULLABLE                                                        | Refund timestamp                                                        |
| processed_by_user_id | BIGINT        | FOREIGN KEY → users(user_id), NULLABLE                          | User who processed the deposit                                          |
| created_at           | TIMESTAMP     | NULLABLE                                                        | Record creation timestamp                                               |
| updated_at           | TIMESTAMP     | NULLABLE                                                        | Record update timestamp                                                 |

**Foreign Keys:**

-   `booking_id` → `bookings(booking_id)` ON DELETE CASCADE
-   `invoice_id` → `invoices(invoice_id)` ON DELETE SET NULL
-   `processed_by_user_id` → `users(user_id)`

---

### 11. refunds

Stores refund transactions for canceled bookings.

| Column              | Data Type     | Constraints                                  | Description                        |
| ------------------- | ------------- | -------------------------------------------- | ---------------------------------- |
| refund_id           | BIGINT        | PRIMARY KEY, AUTO INCREMENT                  | Unique identifier for each refund  |
| booking_id          | BIGINT        | FOREIGN KEY → bookings(booking_id)           | Associated booking                 |
| payment_id          | BIGINT        | FOREIGN KEY → payments(payment_id)           | Associated payment being refunded  |
| invoice_id          | BIGINT        | FOREIGN KEY → invoices(invoice_id), NULLABLE | Associated invoice                 |
| refunded_by_user_id | BIGINT        | FOREIGN KEY → users(user_id)                 | User who processed the refund      |
| refund_amount       | DECIMAL(10,2) | NOT NULL                                     | Amount refunded                    |
| refund_method       | VARCHAR(255)  | NOT NULL, CHECK                              | Refund method                      |
| reference_number    | VARCHAR(255)  | NULLABLE                                     | Transaction reference (for GCash)  |
| refund_date         | DATE          | NOT NULL                                     | Date of refund                     |
| cancellation_reason | TEXT          | NOT NULL                                     | Reason for the refund/cancellation |
| status              | VARCHAR(255)  | NOT NULL, CHECK                              | Refund status                      |
| created_at          | TIMESTAMP     | NULLABLE                                     | Record creation timestamp          |
| updated_at          | TIMESTAMP     | NULLABLE                                     | Record update timestamp            |

**Foreign Keys:**

-   `booking_id` → `bookings(booking_id)`
-   `payment_id` → `payments(payment_id)`
-   `invoice_id` → `invoices(invoice_id)`
-   `refunded_by_user_id` → `users(user_id)`

**Constraints:**

-   `CHK_RefundMethod`: refund_method IN ('Cash', 'GCash')
-   `CHK_RefundStatus`: status IN ('Pending', 'Processed', 'Completed')

---

### 12. assets

Stores dormitory assets and equipment.

| Column        | Data Type    | Constraints                            | Description                                  |
| ------------- | ------------ | -------------------------------------- | -------------------------------------------- |
| asset_id      | BIGINT       | PRIMARY KEY, AUTO INCREMENT            | Unique identifier for each asset             |
| room_id       | BIGINT       | FOREIGN KEY → rooms(room_id), NULLABLE | Room where asset is located (null = storage) |
| name          | VARCHAR(255) | NOT NULL                               | Asset name (e.g., AC Unit, Bed Frame)        |
| condition     | VARCHAR(255) | NOT NULL, CHECK                        | Asset condition                              |
| date_acquired | DATE         | NULLABLE                               | Acquisition date                             |
| created_at    | TIMESTAMP    | NULLABLE                               | Record creation timestamp                    |
| updated_at    | TIMESTAMP    | NULLABLE                               | Record update timestamp                      |

**Foreign Keys:**

-   `room_id` → `rooms(room_id)`

**Constraints:**

-   `CHK_AssetCondition`: condition IN ('Good', 'Needs Repair', 'Broken', 'Missing')

---

### 13. maintenance_logs

Stores maintenance requests and repair logs.

| Column            | Data Type    | Constraints                              | Description                     |
| ----------------- | ------------ | ---------------------------------------- | ------------------------------- |
| log_id            | BIGINT       | PRIMARY KEY, AUTO INCREMENT              | Unique identifier for each log  |
| asset_id          | BIGINT       | FOREIGN KEY → assets(asset_id), NULLABLE | Related asset (if applicable)   |
| logged_by_user_id | BIGINT       | FOREIGN KEY → users(user_id)             | User who logged the maintenance |
| description       | TEXT         | NOT NULL                                 | Description of the issue        |
| notes             | TEXT         | NULLABLE                                 | Additional notes                |
| date_reported     | DATE         | NOT NULL                                 | Date the issue was reported     |
| status            | VARCHAR(255) | NOT NULL, CHECK                          | Maintenance status              |
| date_completed    | DATE         | NULLABLE                                 | Date completed                  |
| created_at        | TIMESTAMP    | NULLABLE                                 | Record creation timestamp       |
| updated_at        | TIMESTAMP    | NULLABLE                                 | Record update timestamp         |

**Foreign Keys:**

-   `asset_id` → `assets(asset_id)`
-   `logged_by_user_id` → `users(user_id)`

**Constraints:**

-   `CHK_MaintenanceStatus`: status IN ('Pending', 'In Progress', 'Completed', 'Cancelled')

---

### 14. electric_readings

Stores electricity meter readings for rooms.

| Column          | Data Type    | Constraints                  | Description                        |
| --------------- | ------------ | ---------------------------- | ---------------------------------- |
| reading_id      | BIGINT       | PRIMARY KEY, AUTO INCREMENT  | Unique identifier for each reading |
| room_id         | BIGINT       | FOREIGN KEY → rooms(room_id) | Room where reading was taken       |
| reading_date    | DATE         | NOT NULL                     | Date of the reading                |
| meter_value_kwh | DECIMAL(8,2) | NOT NULL                     | Meter reading in kilowatt-hours    |
| is_billed       | BOOLEAN      | NOT NULL, DEFAULT FALSE      | Whether reading has been invoiced  |
| created_at      | TIMESTAMP    | NULLABLE                     | Record creation timestamp          |
| updated_at      | TIMESTAMP    | NULLABLE                     | Record update timestamp            |

**Foreign Keys:**

-   `room_id` → `rooms(room_id)`

---

### 15. activity_logs

Stores user activity history for audit purposes.

| Column      | Data Type       | Constraints                  | Description                                |
| ----------- | --------------- | ---------------------------- | ------------------------------------------ |
| log_id      | BIGINT          | PRIMARY KEY, AUTO INCREMENT  | Unique identifier for each log             |
| user_id     | BIGINT          | FOREIGN KEY → users(user_id) | User who performed the action              |
| action      | VARCHAR(255)    | NOT NULL                     | Action performed (e.g., "Created Booking") |
| description | TEXT            | NOT NULL                     | Detailed description                       |
| model_type  | VARCHAR(255)    | NULLABLE                     | Affected model type (e.g., "Booking")      |
| model_id    | BIGINT UNSIGNED | NULLABLE                     | ID of the affected record                  |
| created_at  | TIMESTAMP       | NULLABLE                     | Record creation timestamp                  |
| updated_at  | TIMESTAMP       | NULLABLE                     | Record update timestamp                    |

**Foreign Keys:**

-   `user_id` → `users(user_id)`

---

### 16. settings

Stores system configuration settings.

| Column     | Data Type    | Constraints                 | Description               |
| ---------- | ------------ | --------------------------- | ------------------------- |
| id         | BIGINT       | PRIMARY KEY, AUTO INCREMENT | Unique identifier         |
| key        | VARCHAR(255) | NOT NULL, UNIQUE            | Setting key name          |
| value      | VARCHAR(255) | NOT NULL                    | Setting value             |
| created_at | TIMESTAMP    | NULLABLE                    | Record creation timestamp |
| updated_at | TIMESTAMP    | NULLABLE                    | Record update timestamp   |

**Default Settings:**
| Key | Default Value | Description |
|-----|---------------|-------------|
| electricity_rate_per_kwh | 0 | Electricity rate per kWh |
| late_penalty_rate | 5 | Late payment penalty rate |
| late_penalty_type | percentage | Penalty type (percentage/fixed) |
| late_penalty_grace_days | 7 | Grace period before penalty |
| late_penalty_frequency | once | Penalty frequency (once/daily/weekly/monthly) |
| invoice_due_days | 15 | Days until invoice is due |

---

## Entity Relationship Diagram (Simplified)

```
users ─────────────────┬──────────────────────────────┬─────────────────────┐
    │                  │                              │                     │
    │ recorded_by      │ collected_by                 │ logged_by           │ refunded_by
    ▼                  ▼                              ▼                     ▼
bookings ←─────── payments                    maintenance_logs         refunds
    │                  │                              │                     │
    │ booking_id       │ invoice_id                   │ asset_id            │
    ▼                  ▼                              ▼                     │
invoices ──────► invoice_utilities              assets ◄──────────────────┘
    │                                              │
    │                                              │ room_id
    │                                              ▼
    └──────────────────────────────────────────► rooms ◄──── electric_readings
                                                   ▲
                                                   │
    tenants ◄──────────────────────────────── bookings
                                                   │
                                                   │ rate_id
                                                   ▼
                                                 rates ──────► utilities

security_deposits ◄────────── bookings

activity_logs ◄────────────── users

settings (standalone configuration table)
```

---

## Data Dictionary Summary

| Table             | Description                      | Record Count Estimate |
| ----------------- | -------------------------------- | --------------------- |
| users             | System users (owners/caretakers) | Low                   |
| rooms             | Dormitory rooms                  | Low                   |
| rates             | Pricing rates                    | Low                   |
| utilities         | Utility services per rate        | Low                   |
| tenants           | Lodger information               | Medium                |
| bookings          | Room reservations                | High                  |
| invoices          | Billing records                  | High                  |
| invoice_utilities | Invoice line items               | High                  |
| payments          | Payment transactions             | High                  |
| security_deposits | Deposit tracking                 | Medium                |
| refunds           | Refund transactions              | Low                   |
| assets            | Dormitory equipment              | Medium                |
| maintenance_logs  | Repair/maintenance records       | Medium                |
| electric_readings | Electricity meter readings       | High                  |
| activity_logs     | User activity audit trail        | Very High             |
| settings          | System configuration             | Very Low              |

---

## Notes

1. **Primary Keys**: All tables use auto-incrementing BIGINT primary keys with custom naming (e.g., `user_id`, `room_id`).

2. **Timestamps**: All tables include `created_at` and `updated_at` columns managed by Laravel.

3. **Soft Deletes**: Not implemented; records use status fields for logical deletion where applicable.

4. **Check Constraints**: SQL Server CHECK constraints are used instead of MySQL ENUM types for data validation.

5. **Foreign Key Actions**: Most foreign keys use default RESTRICT behavior; some use CASCADE or SET NULL on delete.

6. **Decimal Precision**: All monetary values use DECIMAL(10,2) for precise calculations.
