DROP TABLE IF EXISTS `course`;
DROP TABLE IF EXISTS `student`;
DROP TABLE IF EXISTS `department`;
-- -----------------------------------------------------
-- Table `academic_year`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `academic_year`;
CREATE TABLE IF NOT EXISTS `academic_year` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `active` TINYINT(1) DEFAULT 1,
  `start_month` VARCHAR(5) NOT NULL, 
  `end_month` VARCHAR(5) NOT NULL,
  `start_year` YEAR NOT NULL, 
  `end_year` YEAR NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  `name` VARCHAR(15) GENERATED ALWAYS AS (CONCAT(`start_year`, '-', `end_year`)) VIRTUAL,
  PRIMARY KEY (`id`)
);
CREATE INDEX academic_year_active_idx1 ON `academic_year` (`active`);
CREATE INDEX academic_year_start_month_idx1 ON `academic_year` (`start_month`);
CREATE INDEX academic_year_end_month_idx1 ON `academic_year` (`end_month`);
CREATE INDEX academic_year_start_year_idx1 ON `academic_year` (`start_year`);
CREATE INDEX academic_year_end_year_idx1 ON `academic_year` (`end_year`);
CREATE INDEX academic_year_archived_idx1 ON `academic_year` (`archived`);
CREATE INDEX academic_year_name_idx1 ON `academic_year` (`name`);
INSERT INTO `academic_year` (`start_month`, `start_year`, `end_month`, `end_year`) VALUES ('Sep', '2023', 'Jun', '2024');

-- -----------------------------------------------------
-- Table `semester`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `semester`;
CREATE TABLE IF NOT EXISTS `semester` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `active` TINYINT(1) DEFAULT 1,
  `name` INT NOT NULL,
  `course_registration_opened` TINYINT(1) DEFAULT 0,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_academic_year` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_semester_academic_year1` FOREIGN KEY (`fk_academic_year`) REFERENCES `academic_year` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
ALTER TABLE `semester` 
ADD COLUMN `registration_end` DATE DEFAULT CURRENT_DATE() AFTER `course_registration_opened`,
ADD COLUMN `exam_results_uploaded` TINYINT(1) DEFAULT 0 AFTER `registration_end`;
CREATE INDEX semester_active_idx1 ON `semester` (`active`);
CREATE INDEX semester_name_idx1 ON `semester` (`name`);
CREATE INDEX semester_course_registration_opened_idx1 ON `semester` (`course_registration_opened`);
CREATE INDEX semester_archived_idx1 ON `semester` (`archived`);
INSERT INTO `semester` (`name`, `course_registration_opened`, `fk_academic_year`) VALUES ('SEMESTER 1', 1, 1);

-- -----------------------------------------------------
-- Table `department`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`)
);
CREATE INDEX department_archived_idx1 ON `department` (`archived`);
INSERT INTO `department`(`name`) VALUES ('ICT'), ('MARINE'), ('NAUTICAL'), ('ELECTRICAL'), ('TRANSPORT'), ('BUSINESS');

-- -----------------------------------------------------
-- Table `course_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course_category`;
CREATE TABLE IF NOT EXISTS `course_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(25) NOT NULL UNIQUE,
  `archived` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`)
);
CREATE INDEX course_category_archived_idx1 ON `course_category` (`archived`);
INSERT INTO `course_category`(`name`) VALUES ('compulsory'), ('elective');

-- -----------------------------------------------------
-- Table `course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `code` VARCHAR(10) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `credits` INT DEFAULT 0,
  `semester` INT NOT NULL,
  `level` INT NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_category` INT ,
  `fk_department` INT NULL,
  PRIMARY KEY (`code`),
  CONSTRAINT `fk_course_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
ALTER TABLE `course` ADD COLUMN `fk_category` INT AFTER `archived`,
ADD CONSTRAINT `fk_course_category1` FOREIGN KEY (`fk_category`) REFERENCES `course_category` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
CREATE INDEX course_name_idx1 ON `course` (`name`);
CREATE INDEX course_credits_idx1 ON `course` (`credits`);
CREATE INDEX course_semester_idx1 ON `course` (`semester`);
CREATE INDEX course_level_idx1 ON `course` (`level`);
CREATE INDEX course_archived_idx1 ON `course` (`archived`);

-- -----------------------------------------------------
-- Table `room`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `room`;
CREATE TABLE IF NOT EXISTS `room` (
  `number` VARCHAR(10) NOT NULL,
  `capacity`INT NOT NULL,
  `location` VARCHAR(255),
  `longitude` VARCHAR(255),
  `latitude` VARCHAR(255),
  `archived` TINYINT(1) DEFAULT 0,
  `fk_department` INT NULL,
  PRIMARY KEY (`number`),
  CONSTRAINT `fk_room_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX room_code_idx1 ON `room` (`capacity`);
CREATE INDEX room_name_idx1 ON `room` (`location`);
CREATE INDEX room_archived_idx1 ON `room` (`archived`);

-- -----------------------------------------------------
-- Table `class`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `class`;
CREATE TABLE IF NOT EXISTS `class` (
  `code` VARCHAR(10) NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_program` INT NOT NULL,
  PRIMARY KEY (`code`),
  CONSTRAINT `fk_class_program1`FOREIGN KEY (`fk_program`) REFERENCES `programs` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX class_archived_idx1 ON `class` (`archived`);

-- -----------------------------------------------------
-- Table `student`
-- -----------------------------------------------------
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
  `archived` TINYINT(1) DEFAULT 0,
  `fk_academic_year` INT NULL,
  `fk_applicant` INT NULL,
  `fk_department` INT NULL,
  `fk_program` INT NULL,
  `fk_class` VARCHAR(10) NULL,
  PRIMARY KEY (`index_number`),
  CONSTRAINT `fk_student_academic_year1` FOREIGN KEY (`fk_academic_year`) REFERENCES `academic_year` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_applicant1` FOREIGN KEY (`fk_applicant`) REFERENCES `applicants_login` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_program1` FOREIGN KEY (`fk_program`) REFERENCES `programs` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_class1` FOREIGN KEY (`fk_class`) REFERENCES `class` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE
);
ALTER TABLE `student` 
ADD COLUMN `level_admitted` INT DEFAULT 100 AFTER `stream_admitted`, 
ADD COLUMN `default_password` TINYINT(1) DEFAULT 1 AFTER `level_admitted`;
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
CREATE INDEX `student_level_admitted_idx1` ON `student` (`level_admitted`);
CREATE INDEX `student_default_password_idx1` ON `student` (`default_password`);
CREATE INDEX `student_archived_idx1` ON `student` (`archived`);

-- -----------------------------------------------------
-- Table `curriculum`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `curriculum`;
CREATE TABLE IF NOT EXISTS `curriculum` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_program` INT NULL,
  `fk_course` VARCHAR(10) NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_curriculum_program1` FOREIGN KEY (`fk_program`) REFERENCES `programs` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_curriculum_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX curriculum_archived_idx1 ON `curriculum` (`archived`);

-- -----------------------------------------------------
-- Table `section`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_class` VARCHAR(10) NULL,
  `fk_course` VARCHAR(10) NULL,
  `fk_semester` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_section_class1` FOREIGN KEY (`fk_class`) REFERENCES `class` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_section_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_section_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX section_archived_idx1 ON `section` (`archived`);

-- -----------------------------------------------------
-- Table `schedule`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `day_of_week` VARCHAR(10) NOT NULL,
  `course_crdt_hrs` INT DEFAULT 0,
  `start_time` TIME NOT NULL,
  `minutes` INT DEFAULT 50,
  `end_time` TIME GENERATED ALWAYS AS (`start_time` + (`course_crdt_hrs` * `minutes`)),
  `archived` TINYINT(1) DEFAULT 0,
  `fk_course` VARCHAR(10) NULL,
  `fk_room` VARCHAR(10) NULL,
  `fk_semester` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_schedule_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_room1` FOREIGN KEY (`fk_room`) REFERENCES `room` (`number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX schedule_day_of_week_idx1 ON `schedule` (`day_of_week`);
CREATE INDEX schedule_course_crdt_hrs_idx1 ON `schedule` (`course_crdt_hrs`);
CREATE INDEX schedule_start_time_idx1 ON `schedule` (`start_time`);
CREATE INDEX schedule_minutes_idx1 ON `schedule` (`minutes`);
CREATE INDEX schedule_end_time_idx1 ON `schedule` (`end_time`);
CREATE INDEX schedule_archived_idx1 ON `schedule` (`archived`);

-- -----------------------------------------------------
-- Table `course_registration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course_registration`;
CREATE TABLE IF NOT EXISTS `course_registration` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_course` VARCHAR(10) NULL,
  `fk_student` VARCHAR(10) NULL,
  `fk_semester` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_course_registration_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_course_registration_student1` FOREIGN KEY (`fk_student`) REFERENCES `student` (`index_number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_course_registration_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
ALTER TABLE `course_registration` ADD COLUMN `registered` TINYINT(1) DEFAULT 0 AFTER `fk_semester`;
CREATE INDEX course_registration_archived_idx1 ON `course_registration` (`archived`);
CREATE INDEX course_registration_registered_idx1 ON `course_registration` (`registered`);

-- -----------------------------------------------------
-- Table `staff`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `number` VARCHAR(10) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(255) NOT NULL,
  `middle_name` VARCHAR(255),
  `last_name` VARCHAR(255) NOT NULL,
  `prefix` VARCHAR(10),
  `gender` VARCHAR(1) DEFAULT 'F',
  `role` VARCHAR(15) NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_department` INT NULL,
  PRIMARY KEY (`number`),
  CONSTRAINT `fk_staff_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX staff_email_idx1 ON `staff` (`email`);
CREATE INDEX staff_first_name_idx1 ON `staff` (`first_name`);
CREATE INDEX staff_last_name_idx1 ON `staff` (`last_name`);
CREATE INDEX staff_gender_idx1 ON `staff` (`gender`);
CREATE INDEX staff_role_idx1 ON `staff` (`role`);
CREATE INDEX staff_archived_idx1 ON `staff` (`archived`);

-- -----------------------------------------------------
-- Table `lecture`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lecture`;
CREATE TABLE IF NOT EXISTS `lecture` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_staff` VARCHAR(20) NULL,
  `fk_section` INT NULL,
  `fk_semester` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_lecture_staff1` FOREIGN KEY (`fk_staff`) REFERENCES `staff` (`number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_lecture_section1` FOREIGN KEY (`fk_section`) REFERENCES `section` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `lecture_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX lecture_archived_idx1 ON `lecture` (`archived`);

ALTER TABLE `admission_period` ADD CONSTRAINT `fk_admission_period_academic_year` FOREIGN KEY (`fk_academic_year`) REFERENCES `academic_year`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE; 