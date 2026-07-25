-- =========================================================
-- NEST PH - MVP Database Schema
-- Scope: Single dorm (B2B multi-tenancy not yet implemented)
-- Purpose: Run this script in MySQL, then use MySQL Workbench
--          "Database > Reverse Engineer" to auto-generate ERD
-- =========================================================

CREATE DATABASE IF NOT EXISTS nest_ph_mvp;
USE nest_ph_mvp;

-- ---------------------------------------------------------
-- 1. ROLES (only 2 for now: tenant, admin — "owner" is just an
--    admin with full admin_privileges, not a separate role)
-- ---------------------------------------------------------
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE            -- 'tenant', 'admin'
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 2. USERS (system accounts: admins, tenants w/ login)
-- ---------------------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,                     -- matches Laravel Breeze default column
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,                 -- matches Laravel Breeze default column
    role_id BIGINT UNSIGNED NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3. ADMIN_PRIVILEGES (granular permissions per admin user;
--    an "owner" test account = admin with ALL privileges granted)
-- ---------------------------------------------------------
CREATE TABLE admin_privileges (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    privilege_name ENUM(
        'manage_tenants',
        'manage_rooms',
        'manage_contracts',
        'manage_billing',
        'manage_users',
        'view_reports'
    ) NOT NULL,
    granted_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_privileges_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    UNIQUE KEY uq_user_privilege (user_id, privilege_name)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4. TENANTS (dormer profile info, separate from login account)
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
-- 5. ROOMS
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
-- 6. BEDS
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
-- 7. MAINTENANCE_TICKETS (tenant-reported issues per bed/room)
-- ---------------------------------------------------------
CREATE TABLE maintenance_tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    bed_id BIGINT UNSIGNED NULL,                      -- nullable: issue might be room-wide, not bed-specific
    title VARCHAR(150) NOT NULL,
    category ENUM('electrical', 'plumbing', 'furniture', 'cleanliness', 'other') NOT NULL DEFAULT 'other',
    description TEXT NULL,
    attachment_url VARCHAR(255) NULL,
    status ENUM('open', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'open',
    assigned_to BIGINT UNSIGNED NULL,                 -- FK to users (staff/admin handling it)
    submitted_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    resolved_by BIGINT UNSIGNED NULL,
    CONSTRAINT fk_tickets_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_tickets_bed
        FOREIGN KEY (bed_id) REFERENCES beds(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_tickets_assigned
        FOREIGN KEY (assigned_to) REFERENCES users(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_tickets_resolved_by
        FOREIGN KEY (resolved_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 8. INQUIRIES (public-facing entry point of the funnel)
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
-- 9. LEASE_CONTRACTS (application -> contract, simplified into one table for MVP)
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
-- 10. BILLING_STATEMENTS
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
-- 11. PAYMENTS
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

-- ---------------------------------------------------------
-- 12. ESCALATION_LOGS (delinquency / issue escalation tracking,
--     linked to a tenant and optionally a billing statement)
-- ---------------------------------------------------------
CREATE TABLE escalation_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    billing_id BIGINT UNSIGNED NULL,
    stage TINYINT UNSIGNED NOT NULL DEFAULT 1,        -- 1st notice, 2nd notice, final notice, etc.
    action_type VARCHAR(50) NULL,                      -- e.g. 'email_notice', 'sms_notice', 'account_lock'
    message_content TEXT NULL,
    status ENUM('pending', 'sent', 'resolved') NOT NULL DEFAULT 'pending',
    performed_by BIGINT UNSIGNED NULL,                 -- admin who triggered it (nullable = system-triggered)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_escalation_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_escalation_billing
        FOREIGN KEY (billing_id) REFERENCES billing_statements(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_escalation_admin
        FOREIGN KEY (performed_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- SEED DATA — test accounts for Week 2 Monday task
-- (1 tenant, 1 admin, 1 owner — owner = admin w/ ALL privileges)
-- Note: passwords below are PLAIN TEXT placeholders only.
-- Lopi should re-hash these using Laravel's Hash::make() in the
-- actual seeder — never insert plain-text passwords in production.
-- =========================================================

INSERT INTO roles (role_name) VALUES ('tenant'), ('admin');

-- Test user accounts (password placeholder: "password123")
INSERT INTO users (name, email, password, role_id, is_active) VALUES
('Test Tenant', 'tenant@nestph.test', 'password123', (SELECT id FROM roles WHERE role_name = 'tenant'), 1),
('Test Admin', 'admin@nestph.test', 'password123', (SELECT id FROM roles WHERE role_name = 'admin'), 1),
('Test Owner', 'owner@nestph.test', 'password123', (SELECT id FROM roles WHERE role_name = 'admin'), 1);

-- Owner gets ALL privileges
INSERT INTO admin_privileges (user_id, privilege_name)
SELECT (SELECT id FROM users WHERE email = 'owner@nestph.test'), p.privilege_name
FROM (
    SELECT 'manage_tenants' AS privilege_name
    UNION ALL SELECT 'manage_rooms'
    UNION ALL SELECT 'manage_contracts'
    UNION ALL SELECT 'manage_billing'
    UNION ALL SELECT 'manage_users'
    UNION ALL SELECT 'view_reports'
) p;

-- Regular admin gets a limited subset (no manage_users)
INSERT INTO admin_privileges (user_id, privilege_name) VALUES
((SELECT id FROM users WHERE email = 'admin@nestph.test'), 'manage_tenants'),
((SELECT id FROM users WHERE email = 'admin@nestph.test'), 'manage_rooms'),
((SELECT id FROM users WHERE email = 'admin@nestph.test'), 'manage_billing'),
((SELECT id FROM users WHERE email = 'admin@nestph.test'), 'view_reports');

-- =========================================================
-- END OF MVP SCHEMA
-- Deferred to later sprints (not created here):
--   - ticket_messages (thread of replies per maintenance_ticket)
--   - delinquency_escalations (merged into escalation_logs for now)
--   - audit_logs
--   - documents
--   - dashboard_snapshots
--   - otp_tokens (not needed for MVP scope)
--   - dorms/clients table (only needed once B2B multi-tenant is implemented)
-- =========================================================