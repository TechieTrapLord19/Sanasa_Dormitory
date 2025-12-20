<?php
/**
 * COMPLETE DATABASE REBUILD SCRIPT
 * Converted from SQL Server schema to MySQL
 * Run this on the web server to recreate the exact database schema
 */

error_reporting(0);
ini_set('display_errors', 0);
ini_set('max_execution_time', 600);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<pre>";
echo "========================================\n";
echo "   COMPLETE DATABASE REBUILD\n";
echo "   From SQL Server Schema Export\n";
echo "========================================\n\n";

// Step 1: Drop ALL existing tables
echo "--- DROPPING ALL TABLES ---\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');

$tables = DB::select('SHOW TABLES');
$dbName = 'Tables_in_' . env('DB_DATABASE');
foreach ($tables as $table) {
    try {
        Schema::drop($table->$dbName);
        echo "Dropped: {$table->$dbName}\n";
    } catch (Exception $e) {}
}

echo "\n--- CREATING TABLES ---\n";

// migrations table (Laravel internal)
DB::statement("
CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(191) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ migrations\n";

// users table
DB::statement("
CREATE TABLE users (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(191) NOT NULL,
    middle_name VARCHAR(191) NULL,
    last_name VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    role VARCHAR(255) NOT NULL DEFAULT 'caretaker',
    email_verified_at DATETIME NULL,
    password VARCHAR(191) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    birth_date DATE NULL,
    address TEXT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ users\n";

// password_reset_tokens table
DB::statement("
CREATE TABLE password_reset_tokens (
    email VARCHAR(191) NOT NULL PRIMARY KEY,
    token VARCHAR(191) NOT NULL,
    created_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ password_reset_tokens\n";

// sessions table
DB::statement("
CREATE TABLE sessions (
    id VARCHAR(191) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ sessions\n";

// cache table
DB::statement("
CREATE TABLE cache (
    `key` VARCHAR(191) NOT NULL PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ cache\n";

// cache_locks table
DB::statement("
CREATE TABLE cache_locks (
    `key` VARCHAR(191) NOT NULL PRIMARY KEY,
    owner VARCHAR(191) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ cache_locks\n";

// jobs table
DB::statement("
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(191) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ jobs\n";

// job_batches table
DB::statement("
CREATE TABLE job_batches (
    id VARCHAR(191) NOT NULL PRIMARY KEY,
    name VARCHAR(191) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ job_batches\n";

// failed_jobs table
DB::statement("
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(191) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ failed_jobs\n";

// activity_logs table
DB::statement("
CREATE TABLE activity_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(191) NOT NULL,
    description TEXT NOT NULL,
    model_type VARCHAR(191) NULL,
    model_id BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ activity_logs\n";

// rooms table
DB::statement("
CREATE TABLE rooms (
    room_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_num VARCHAR(191) NOT NULL UNIQUE,
    floor VARCHAR(191) NOT NULL,
    capacity INT NOT NULL,
    status VARCHAR(191) NOT NULL DEFAULT 'available',
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ rooms\n";

// assets table
DB::statement("
CREATE TABLE assets (
    asset_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NULL,
    name VARCHAR(191) NOT NULL,
    `condition` VARCHAR(191) NOT NULL,
    date_acquired DATE NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ assets\n";

// rates table
DB::statement("
CREATE TABLE rates (
    rate_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    duration_type VARCHAR(191) NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    rate_name VARCHAR(191) NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ rates\n";

// utilities table
DB::statement("
CREATE TABLE utilities (
    utilities_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rate_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(191) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (rate_id) REFERENCES rates(rate_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ utilities\n";

// tenants table
DB::statement("
CREATE TABLE tenants (
    tenant_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(191) NOT NULL,
    middle_name VARCHAR(191) NULL,
    last_name VARCHAR(191) NOT NULL,
    email VARCHAR(191) NULL,
    address TEXT NULL,
    birth_date DATE NULL,
    id_document VARCHAR(191) NULL,
    contact_num VARCHAR(191) NULL,
    emer_contact_num VARCHAR(191) NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'active',
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    emer_contact_name VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ tenants\n";

// bookings table
DB::statement("
CREATE TABLE bookings (
    booking_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    rate_id BIGINT UNSIGNED NOT NULL,
    recorded_by_user_id BIGINT UNSIGNED NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    total_calculated_fee DECIMAL(10,2) NOT NULL,
    status VARCHAR(191) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    cancellation_reason TEXT NULL,
    secondary_tenant_id BIGINT UNSIGNED NULL,
    checked_in_at DATETIME NULL,
    checked_out_at DATETIME NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id),
    FOREIGN KEY (rate_id) REFERENCES rates(rate_id),
    FOREIGN KEY (recorded_by_user_id) REFERENCES users(user_id),
    FOREIGN KEY (secondary_tenant_id) REFERENCES tenants(tenant_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ bookings\n";

// invoices table
DB::statement("
CREATE TABLE invoices (
    invoice_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    date_generated DATE NOT NULL,
    rent_subtotal DECIMAL(10,2) NOT NULL,
    utility_electricity_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_due DECIMAL(10,2) NOT NULL,
    is_paid TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    due_date DATE NULL,
    penalty_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    days_overdue INT NOT NULL DEFAULT 0,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ invoices\n";

// invoice_utilities table
DB::statement("
CREATE TABLE invoice_utilities (
    invoice_utility_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    utility_name VARCHAR(191) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ invoice_utilities\n";

// payments table
DB::statement("
CREATE TABLE payments (
    payment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NULL,
    collected_by_user_id BIGINT UNSIGNED NULL,
    payment_type VARCHAR(191) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(191) NOT NULL,
    reference_number VARCHAR(191) NULL,
    date_received DATETIME NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    booking_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE,
    FOREIGN KEY (collected_by_user_id) REFERENCES users(user_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ payments\n";

// refunds table
DB::statement("
CREATE TABLE refunds (
    refund_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id BIGINT UNSIGNED NOT NULL,
    refunded_by_user_id BIGINT UNSIGNED NOT NULL,
    refund_amount DECIMAL(10,2) NOT NULL,
    refund_method VARCHAR(191) NOT NULL,
    reference_number VARCHAR(191) NULL,
    refund_date DATE NOT NULL,
    cancellation_reason TEXT NOT NULL,
    status VARCHAR(191) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id),
    FOREIGN KEY (refunded_by_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ refunds\n";

// security_deposits table
DB::statement("
CREATE TABLE security_deposits (
    security_deposit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED NULL,
    amount_required DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0,
    amount_deducted DECIMAL(10,2) NOT NULL DEFAULT 0,
    amount_refunded DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(191) NOT NULL DEFAULT 'Pending',
    deduction_reason TEXT NULL,
    notes TEXT NULL,
    refunded_at DATETIME NULL,
    processed_by_user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE SET NULL,
    FOREIGN KEY (processed_by_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ security_deposits\n";

// electric_readings table
DB::statement("
CREATE TABLE electric_readings (
    reading_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    reading_date DATE NOT NULL,
    meter_value_kwh DECIMAL(8,2) NOT NULL,
    is_billed TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ electric_readings\n";

// expenses table
DB::statement("
CREATE TABLE expenses (
    expense_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    description TEXT NULL,
    amount DECIMAL(10,2) NOT NULL,
    expense_date DATE NOT NULL,
    receipt_number VARCHAR(100) NULL,
    recorded_by_user_id BIGINT UNSIGNED NOT NULL,
    notes TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    asset_type VARCHAR(100) NULL,
    FOREIGN KEY (recorded_by_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ expenses\n";

// maintenance_logs table
DB::statement("
CREATE TABLE maintenance_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id BIGINT UNSIGNED NULL,
    logged_by_user_id BIGINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    date_reported DATE NOT NULL,
    status VARCHAR(191) NOT NULL,
    date_completed DATE NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    notes TEXT NULL,
    FOREIGN KEY (asset_id) REFERENCES assets(asset_id) ON DELETE SET NULL,
    FOREIGN KEY (logged_by_user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ maintenance_logs\n";

// settings table
DB::statement("
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(191) NOT NULL UNIQUE,
    value VARCHAR(191) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ settings\n";

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n========================================\n";
echo "   ALL TABLES CREATED!\n";
echo "========================================\n";

// Now run seeders
echo "\n--- RUNNING SEEDERS ---\n";

$seeders = [
    'Database\\Seeders\\UserSeeder',
    'Database\\Seeders\\RoomSeeder',
    'Database\\Seeders\\TenantSeeder',
    'Database\\Seeders\\RoomAssetSeeder',
    'Database\\Seeders\\RateSeeder',
    'Database\\Seeders\\SettingSeeder',
];

foreach ($seeders as $seeder) {
    try {
        Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
        echo "✓ " . class_basename($seeder) . "\n";
    } catch (Exception $e) {
        echo "✗ " . class_basename($seeder) . ": " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "   REBUILD COMPLETE!\n";
echo "========================================\n";
echo "\nLogin: owner@app.com / lloren123\n";
echo "</pre>";
