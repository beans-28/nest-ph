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
    granted_by BIGINT UNSIGNED NULL,                  -- which admin/owner granted this privilege
    granted_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_privileges_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_privileges_granted_by
        FOREIGN KEY (granted_by) REFERENCES users(id)
        ON DELETE SET NULL,
    UNIQUE KEY uq_user_privilege (user_id, privilege_name)
) ENGINE=InnoDB;

-- Explicit indexes (July 23 task): email already has a UNIQUE constraint
-- above which auto-creates an index; role_id gets an explicit index here
-- since it's a common lookup/filter column.
CREATE INDEX idx_users_role_id ON users(role_id);

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
    created_by BIGINT UNSIGNED NULL,               -- admin who registered this tenant (onboarding audit trail)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tenants_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_tenants_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 5. FLOORS
-- ---------------------------------------------------------
CREATE TABLE floors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    floor_name VARCHAR(20) NOT NULL,                -- e.g. "1st Floor", "Mezzanine"
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 6. ROOMS
-- ---------------------------------------------------------
CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    floor_id BIGINT UNSIGNED NOT NULL,
    room_no VARCHAR(20) NOT NULL,
    room_type VARCHAR(50) NULL,                     -- e.g. shared, solo, capsule
    capacity INT UNSIGNED NOT NULL DEFAULT 1,       -- max number of beds/occupants
    monthly_rate DECIMAL(10,2) NOT NULL DEFAULT 0,
    vr_asset_path VARCHAR(255) NULL,                -- path/URL to VR tour asset (Railway volume or external bucket TBD w/ Backend)
    status ENUM('available', 'full', 'maintenance') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rooms_floor
        FOREIGN KEY (floor_id) REFERENCES floors(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 7. BEDS
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
-- 8. MAINTENANCE_TICKETS (tenant-reported issues per bed/room)
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
-- 9. INQUIRIES (public-facing entry point of the funnel)
-- ---------------------------------------------------------
CREATE TABLE inquiries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    contact_number VARCHAR(20) NULL,
    email VARCHAR(150) NULL,
    room_id BIGINT UNSIGNED NULL,                   -- nullable: inquiry may not target a specific room yet
    message TEXT NULL,
    preferred_room_type VARCHAR(50) NULL,
    dpa_consent TINYINT(1) NOT NULL DEFAULT 0,       -- Data Privacy Act consent checkbox
    status ENUM('new', 'contacted', 'converted', 'closed') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_inquiries_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Index for inquiry lookups by status and date (Aug 6 task)
CREATE INDEX idx_inquiries_status_date ON inquiries(status, created_at);

-- ---------------------------------------------------------
-- 10. APPLICATIONS (pending stage — before a contract is signed)
-- ---------------------------------------------------------
CREATE TABLE applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inquiry_id BIGINT UNSIGNED NULL,                -- traceability back to originating inquiry
    tenant_id BIGINT UNSIGNED NOT NULL,
    bed_id BIGINT UNSIGNED NOT NULL,
    preferred_start_date DATE NULL,
    dpa_consent TINYINT(1) NOT NULL DEFAULT 0,       -- Data Privacy Act consent for this application
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    created_by BIGINT UNSIGNED NULL,                 -- admin who processed/logged the application
    approved_by BIGINT UNSIGNED NULL,                -- admin who approved/rejected it
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_applications_inquiry
        FOREIGN KEY (inquiry_id) REFERENCES inquiries(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_applications_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_applications_bed
        FOREIGN KEY (bed_id) REFERENCES beds(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_applications_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_applications_approved_by
        FOREIGN KEY (approved_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Index for application lookups by status and date (Aug 6 task)
CREATE INDEX idx_applications_status_date ON applications(status, created_at);

-- ---------------------------------------------------------
-- 11. LEASE_CONTRACTS (signed/active stage, created from an approved application)
-- ---------------------------------------------------------
CREATE TABLE lease_contracts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,        -- the approved application this contract came from
    tenant_id BIGINT UNSIGNED NOT NULL,
    bed_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    monthly_rate DECIMAL(10,2) NOT NULL,
    -- NOTE: signing method placeholder below — team decided to scan physical
    -- paper instead of true e-sign, but exact flow is still pending PM
    -- clarification. Adjust this block once confirmed (e.g. rename to
    -- signed_document_url only, drop esign_status if fully manual scan).
    esign_status ENUM('pending', 'signed', 'not_applicable') NOT NULL DEFAULT 'pending',
    signed_document_url VARCHAR(255) NULL,           -- path/link to scanned signed copy
    signed_at TIMESTAMP NULL,
    status ENUM('pending', 'active', 'terminated', 'expired') NOT NULL DEFAULT 'pending',
    created_by BIGINT UNSIGNED NULL,                 -- admin who created/processed this contract
    approved_by BIGINT UNSIGNED NULL,                -- admin who approved it (may differ from created_by)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_contracts_application
        FOREIGN KEY (application_id) REFERENCES applications(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_contracts_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_contracts_bed
        FOREIGN KEY (bed_id) REFERENCES beds(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_contracts_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_contracts_approved_by
        FOREIGN KEY (approved_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Index for contract/application lookups by status and date (Aug 6 task)
CREATE INDEX idx_contracts_status_date ON lease_contracts(status, start_date);

-- ---------------------------------------------------------
-- 12. BILLING_STATEMENTS
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
-- 13. PAYMENTS
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
-- 14. ESCALATION_STAGES (config/lookup table defining each stage's
--     trigger rule — e.g. stage 1 = 7 days overdue, stage 2 = 15 days)
-- ---------------------------------------------------------
CREATE TABLE escalation_stages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stage_number TINYINT UNSIGNED NOT NULL UNIQUE,
    trigger_condition VARCHAR(100) NOT NULL,        -- e.g. 'overdue_7_days', 'overdue_15_days'
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 15. ESCALATION_LOGS (actual escalation events per tenant,
--     linked to a tenant and optionally a billing statement)
-- ---------------------------------------------------------
CREATE TABLE escalation_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    billing_id BIGINT UNSIGNED NULL,
    stage_id BIGINT UNSIGNED NOT NULL,
    action_type VARCHAR(50) NULL,                      -- e.g. 'email_notice', 'sms_notice', 'account_lock'
    message_content TEXT NULL,
    notified TINYINT(1) NOT NULL DEFAULT 0,            -- whether the tenant has actually been notified
    status ENUM('pending', 'sent', 'resolved') NOT NULL DEFAULT 'pending',
    performed_by BIGINT UNSIGNED NULL,                 -- admin who triggered it (nullable = system-triggered)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_escalation_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_escalation_billing
        FOREIGN KEY (billing_id) REFERENCES billing_statements(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_escalation_stage
        FOREIGN KEY (stage_id) REFERENCES escalation_stages(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_escalation_admin
        FOREIGN KEY (performed_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Indexes to support "all overdue tenants past X days" queries (Week 6)
CREATE INDEX idx_billing_status_duedate ON billing_statements(status, due_date);
CREATE INDEX idx_escalation_tenant_created ON escalation_logs(tenant_id, created_at);

-- ---------------------------------------------------------
-- 16. TICKET_MESSAGES (thread of replies per maintenance_ticket)
-- ---------------------------------------------------------
CREATE TABLE ticket_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT UNSIGNED NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,               -- FK to users (tenant or admin/staff replying)
    message_body TEXT NOT NULL,
    sent_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ticket_messages_ticket
        FOREIGN KEY (ticket_id) REFERENCES maintenance_tickets(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_ticket_messages_sender
        FOREIGN KEY (sender_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- Index for ticket status lookups (Week 7)
CREATE INDEX idx_tickets_status ON maintenance_tickets(status);
CREATE INDEX idx_ticket_messages_ticket ON ticket_messages(ticket_id);

-- ---------------------------------------------------------
-- 17. ANNOUNCEMENTS
-- ---------------------------------------------------------
CREATE TABLE announcements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    posted_by BIGINT UNSIGNED NULL,                    -- FK to users (admin who posted it)
    restrict_comments TINYINT(1) NOT NULL DEFAULT 0,   -- if true, tenants can't comment on this announcement
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_announcements_posted_by
        FOREIGN KEY (posted_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_announcements_created ON announcements(created_at);

-- ---------------------------------------------------------
-- 18. ANNOUNCEMENT_COMMENTS
-- ---------------------------------------------------------
CREATE TABLE announcement_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    announcement_id BIGINT UNSIGNED NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_announcement
        FOREIGN KEY (announcement_id) REFERENCES announcements(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_comments_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_comments_announcement ON announcement_comments(announcement_id);

-- ---------------------------------------------------------
-- 19. REVIEWS
-- ---------------------------------------------------------
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,                  -- e.g. 1-5
    comment TEXT NULL,
    verified_tenant TINYINT(1) NOT NULL DEFAULT 0,     -- true if tenant had an actual lease_contract on file
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_reviews_tenant ON reviews(tenant_id);

-- ---------------------------------------------------------
-- 20. DORMITORY_PROFILE (single-row table: public-facing info
--     about the dorm itself — name, description, contact, etc.)
-- ---------------------------------------------------------
CREATE TABLE dormitory_profile (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dorm_name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    address VARCHAR(255) NULL,
    contact_number VARCHAR(20) NULL,
    contact_email VARCHAR(150) NULL,
    logo_path VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

-- Sample floor for initial testing (July 28 task)
INSERT INTO floors (floor_name, description) VALUES
('1st Floor', 'Ground level floor, near common areas and entrance');

-- =========================================================
-- END OF MVP SCHEMA
-- Deferred to later sprints (not created here):
--   - penalties, damages (Week 5 — separate tables w/ auto billing-line-item FK, not yet built)
--   - waive_audit_log (Week 5 — who/when/reason for waived penalties/damages)
--   - delinquency_escalations (merged into escalation_logs for now)
--   - audit_logs
--   - documents
--   - dashboard_snapshots
--   - otp_tokens (not needed for MVP scope)
--   - dorms/clients table (only needed once B2B multi-tenant is implemented)
-- =========================================================