-- Add admin_notes column to therapist_applications table
ALTER TABLE `therapist_applications`
ADD COLUMN `admin_notes` text DEFAULT NULL AFTER `application_status`; 