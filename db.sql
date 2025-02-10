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

/*NOT DONE YET ON LIVE SERVER MAIN DB*/
ALTER TABLE `department` 
ADD COLUMN `hod` VARCHAR(10) AFTER `name`,
ADD CONSTRAINT `fk_department_hod` FOREIGN KEY (`hod`) REFERENCES `staff`(`number`);

ALTER TABLE `department` DROP FOREIGN KEY `fk_department_hod`; 
ALTER TABLE `department` ADD CONSTRAINT `fk_department_hod` FOREIGN KEY (`hod`) REFERENCES `staff`(`number`) ON DELETE SET NULL ON UPDATE CASCADE; 

ALTER TABLE `course` CHANGE `credits` `credit_hours` INT NOT NULL;
ALTER TABLE `course` ADD COLUMN `contact_hours` INT AFTER `credit_hours`;

CREATE TABLE course_index_code (
    `code` VARCHAR(4) PRIMARY KEY,
    `type` VARCHAR(15) NOT NULL
);
INSERT INTO course_index_code (`code`, `type`) VALUES 
('BNS', 'BSC'), ('BME', 'BSC'), ('BMT', 'BSC'), ('BCE', 'BSC'), ('BCS', 'BSC'), 
('BEE', 'BSC'), ('BIT', 'BSC'), ('BPS', 'BSC'), ('BLG', 'BSC'), ('BLM', 'BSC');

ALTER TABLE `section` CHANGE `credits` `credit_hours` INT(11) NOT NULL; 