-- =========================================================
-- NEST PH - MVP Database Schema
-- Scope: Single dorm (B2B multi-tenancy not yet implemented)
-- Purpose: Run this script in MySQL, then use MySQL Workbench
--          "Database > Reverse Engineer" to auto-generate ERD
-- =========================================================

CREATE DATABASE IF NOT EXISTS nest_ph_mvp;
USE nest_ph_mvp;

-- ---------------------------------------------------------
-- 1. USERS (system accounts: admins, staff, tenants w/ login)
-- ---------------------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,                     -- matches Laravel Breeze default column
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,                 -- matches Laravel Breeze default column
    role ENUM('admin', 'staff', 'tenant') NOT NULL DEFAULT 'tenant',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 2. TENANTS (dormer profile info, separate from login account)
-- ---------------------------------------------------------
CREATE TABLE tenants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,                  -- linked login account (nullable: tenant may not have account yet)
    full_name VARCHAR(150) NOT NULL,
    contact_number VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    emergency_contact_name VARCHAR(150) NULL,
    emergency_contact_number VARCHAR(20) NULL,
    is_blacklisted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tenants_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3. ROOMS
-- ---------------------------------------------------------
CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_no VARCHAR(20) NOT NULL,
    floor VARCHAR(10) NULL,
    room_type VARCHAR(50) NULL,                     -- e.g. shared, solo, capsule
    monthly_rate DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('available', 'full', 'maintenance') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4. BEDS
-- ---------------------------------------------------------
CREATE TABLE beds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    bed_label VARCHAR(20) NOT NULL,                 -- e.g. "Bed A", "Lower Bunk 1"
    status ENUM('vacant', 'occupied', 'maintenance') NOT NULL DEFAULT 'vacant',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_beds_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 5. INQUIRIES (public-facing entry point of the funnel)
-- ---------------------------------------------------------
CREATE TABLE inquiries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    contact_number VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    message TEXT NULL,
    preferred_room_type VARCHAR(50) NULL,
    status ENUM('new', 'contacted', 'converted', 'closed') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 6. LEASE_CONTRACTS (application -> contract, simplified into one table for MVP)
-- ---------------------------------------------------------
CREATE TABLE lease_contracts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    bed_id BIGINT UNSIGNED NOT NULL,
    inquiry_id BIGINT UNSIGNED NULL,                -- traceability back to originating inquiry
    start_date DATE NOT NULL,
    end_date DATE NULL,
    monthly_rate DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'active', 'terminated', 'expired') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_contracts_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_contracts_bed
        FOREIGN KEY (bed_id) REFERENCES beds(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_contracts_inquiry
        FOREIGN KEY (inquiry_id) REFERENCES inquiries(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 7. BILLING_STATEMENTS
-- ---------------------------------------------------------
CREATE TABLE billing_statements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contract_id BIGINT UNSIGNED NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    billing_period_start DATE NOT NULL,
    billing_period_end DATE NOT NULL,
    due_date DATE NOT NULL,
    base_rent DECIMAL(10,2) NOT NULL DEFAULT 0,
    penalty_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('unpaid', 'partial', 'paid', 'overdue') NOT NULL DEFAULT 'unpaid',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_billing_contract
        FOREIGN KEY (contract_id) REFERENCES lease_contracts(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_billing_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 8. PAYMENTS
-- ---------------------------------------------------------
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    billing_id BIGINT UNSIGNED NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'gcash', 'bank_transfer', 'other') NOT NULL DEFAULT 'cash',
    reference_number VARCHAR(100) NULL,
    payment_date DATE NOT NULL,
    recorded_by BIGINT UNSIGNED NULL,               -- FK to users (admin/staff who recorded it)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_billing
        FOREIGN KEY (billing_id) REFERENCES billing_statements(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_payments_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_payments_recorded_by
        FOREIGN KEY (recorded_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- END OF MVP SCHEMA
-- Deferred to later sprints (not created here):
--   - maintenance_tickets, ticket_messages
--   - escalation_logs / delinquency_escalations
--   - audit_logs
--   - documents
--   - otp_tokens
--   - dashboard_snapshots
--   - dorms/clients table (only needed once B2B multi-tenant is implemented)
-- =========================================================