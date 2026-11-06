-- Upgrade an existing White Sea Star database to the multi-role account model.
-- Run this in phpMyAdmin → SQL when you do not want to re-import the full schema
-- (re-importing database.sql resets the seeded admin password).
--
-- Existing "member" accounts become "partner" accounts.

ALTER TABLE users
    MODIFY COLUMN role ENUM('super_admin', 'member', 'partner', 'supplier', 'customer')
    NOT NULL DEFAULT 'member';

UPDATE users SET role = 'partner' WHERE role = 'member';

ALTER TABLE users
    MODIFY COLUMN role ENUM('super_admin', 'partner', 'supplier', 'customer')
    NOT NULL DEFAULT 'partner';
