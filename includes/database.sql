-- White Sea Star General Trading L.L.C — complete database setup
-- Import this single file through one.com phpMyAdmin into the database
-- `whiteseastar_comwsssite`.
--
-- Compatible with MySQL 8.0+ and MariaDB 10.5+, utf8mb4, InnoDB.
-- It creates the tables (safe to re-run) and seeds the super-admin account.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'partner', 'supplier', 'customer') NOT NULL DEFAULT 'partner',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    password_changed_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role),
    KEY idx_users_active (is_active),
    CONSTRAINT fk_users_created_by FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS resources (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(180) NOT NULL,
    description VARCHAR(500) NOT NULL DEFAULT '',
    category VARCHAR(80) NOT NULL,
    stored_name VARCHAR(191) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(120) NOT NULL,
    file_size INT UNSIGNED NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_resources_published (is_published, created_at),
    KEY idx_resources_category (category),
    CONSTRAINT fk_resources_created_by FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enquiries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(40) NOT NULL DEFAULT '',
    company VARCHAR(150) NOT NULL DEFAULT '',
    subject VARCHAR(180) NOT NULL DEFAULT '',
    message TEXT NOT NULL,
    status ENUM('new', 'handled') NOT NULL DEFAULT 'new',
    ip VARCHAR(45) NOT NULL DEFAULT '',
    user_agent VARCHAR(255) NOT NULL DEFAULT '',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    handled_at DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_enquiries_status (status, created_at),
    KEY idx_enquiries_email (email),
    KEY idx_enquiries_ip (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(190) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    success TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_attempts_email (email, attempted_at),
    KEY idx_attempts_ip (ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    details VARCHAR(500) NULL,
    ip VARCHAR(45) NOT NULL DEFAULT '',
    user_agent VARCHAR(255) NOT NULL DEFAULT '',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_user (user_id, created_at),
    KEY idx_audit_created (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Super-admin seed. Sign in with admin@whiteseastar.com and change this
-- password immediately after the first login.
INSERT INTO users (name, email, password_hash, role, is_active, password_changed_at)
VALUES (
    'Administrator',
    'admin@whiteseastar.com',
    '$argon2id$v=19$m=65536,t=4,p=1$ZEVWWHB5UWwzcTg3RzNOaQ$fQ/bEupkulMS/ZGgGMBn9+pa4p9ymXr30zjed71c4jY',
    'super_admin',
    1,
    UTC_TIMESTAMP()
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password_hash = VALUES(password_hash),
    role = 'super_admin',
    is_active = VALUES(is_active),
    password_changed_at = UTC_TIMESTAMP();

-- Upgrade older installations that used the single "member" role.
-- The temporary enum keeps existing values valid while they are migrated.
ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'member', 'partner', 'supplier', 'customer') NOT NULL DEFAULT 'member';
UPDATE users SET role = 'partner' WHERE role = 'member';
ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'partner', 'supplier', 'customer') NOT NULL DEFAULT 'partner';
