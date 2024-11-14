ALTER TABLE acceptance_receipts ADD COLUMN `status` TINYINT(1) DEFAULT 0 AFTER `app_login`;
ALTER TABLE form_sections_chek ADD COLUMN `stream_admitted` VARCHAR(30) DEFAULT NULL AFTER `level_admitted`;
ALTER TABLE form_sections_chek ADD COLUMN `shortlisted` TINYINT(1) DEFAULT 0 AFTER `level_admitted`;
ALTER TABLE form_sections_chek 
ADD COLUMN `first_prog_qualified` TINYINT(1) DEFAULT 0 AFTER `emailed_letter`,
ADD COLUMN `second_prog_qualified` TINYINT(1) DEFAULT 0 AFTER `first_prog_qualified`;

ALTER TABLE broadsheets 
DROP COLUMN `any_one_core_passed`,
DROP COLUMN `total_core_score`,
DROP COLUMN `any_three_elective_passed`,
DROP COLUMN `any_two_elective_subjects`,
ADD COLUMN `mode` VARCHAR(20) AFTER `program_id`,
ADD COLUMN `required_core_subjects` TEXT AFTER `required_core_passed`,
ADD COLUMN `total_core_score` INT AFTER `required_core_subjects`,
ADD COLUMN `required_elective_passed` INT AFTER `required_core_subjects`,
ADD COLUMN `required_elective_subjects` TEXT AFTER `required_elective_passed`,
ADD COLUMN `any_elective_subjects` TEXT AFTER `required_elective_subjects`;