ALTER TABLE `users`
    ADD COLUMN `phone_number` VARCHAR(16) NULL AFTER `username`,
    ADD COLUMN `mfa_required` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`,
    ADD COLUMN `ekyc_status` VARCHAR(24) NOT NULL DEFAULT 'not_started' AFTER `mfa_required`;

UPDATE `users`
SET `mfa_required` = 1
WHERE `role` = 'admin';
