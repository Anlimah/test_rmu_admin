DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `index_number` VARCHAR(10) NOT NULL,
  `app_number` VARCHAR(10) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(15) NOT NULL,
  `prefix` VARCHAR(10),
  `first_name` VARCHAR(255) NOT NULL,
  `middle_name` VARCHAR(255),
  `last_name` VARCHAR(255) NOT NULL,
  `suffix` VARCHAR(10),
  `gender` VARCHAR(1) DEFAULT 'F',
  `dob` DATE NOT NULL,
  `nationality` VARCHAR(25) NOT NULL,
  `photo` VARCHAR(255),
  `marital_status` VARCHAR(25),
  `disability` VARCHAR(25),
  `date_admitted` DATE NOT NULL,
  `term_admitted` VARCHAR(15) NOT NULL,
  `stream_admitted` VARCHAR(15) NOT NULL,
  `academic_year_admitted` VARCHAR(15) NOT NULL,
  `program` VARCHAR(255) NOT NULL,
  `department` VARCHAR(255) NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`index_number`)
);
CREATE INDEX `student_phone_number_idx1` ON `student` (`phone_number`);
CREATE INDEX `student_first_name_idx1` ON `student` (`first_name`);
CREATE INDEX `student_last_name_idx1` ON `student` (`last_name`);
CREATE INDEX `student_gender_idx1` ON `student` (`gender`);
CREATE INDEX `student_dob_idx1` ON `student` (`dob`);
CREATE INDEX `student_nationality_idx1` ON `student` (`nationality`);
CREATE INDEX `student_marital_status_idx1` ON `student` (`marital_status`);
CREATE INDEX `student_disability_idx1` ON `student` (`disability`);
CREATE INDEX `student_date_admitted_idx1` ON `student` (`date_admitted`);
CREATE INDEX `student_term_admitted_idx1` ON `student` (`term_admitted`);
CREATE INDEX `student_stream_admitted_idx1` ON `student` (`stream_admitted`);
CREATE INDEX `student_academic_year_admitted_idx1` ON `student` (`academic_year_admitted`);
CREATE INDEX `student_program_idx1` ON `student` (`program`);
CREATE INDEX `student_department_idx1` ON `student` (`department`);
CREATE INDEX `student_archived_idx1` ON `student` (`archived`);

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `archived` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`)
);
CREATE INDEX IF NOT EXISTS department_archived_idx1 ON `departments` (`archived`);

ALTER TABLE `programs` 
DROP COLUMN `department`,
ADD COLUMN `department` INT AFTER `name`;

ALTER TABLE `programs` 
ADD CONSTRAINT `fk_program_department` 
FOREIGN KEY (`department`) REFERENCES `departments` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;