DROP TABLE IF EXISTS `course`;
DROP TABLE IF EXISTS `student`;
DROP TABLE IF EXISTS `department`;
DROP TABLE IF EXISTS `course`;
DROP TABLE IF EXISTS `student`;
DROP TABLE IF EXISTS `department`;
DROP TABLE IF EXISTS `course`;
DROP TABLE IF EXISTS `student`;
DROP TABLE IF EXISTS `academic_year`;
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
  `name` VARCHAR(15) GENERATED ALWAYS AS (CONCAT(`start_year`, '-', `end_year`)) STORED,
  PRIMARY KEY (`id`)
);
CREATE INDEX academic_year_active_idx1 ON `academic_year` (`active`);
CREATE INDEX academic_year_start_month_idx1 ON `academic_year` (`start_month`);
CREATE INDEX academic_year_end_month_idx1 ON `academic_year` (`end_month`);
CREATE INDEX academic_year_start_year_idx1 ON `academic_year` (`start_year`);
CREATE INDEX academic_year_end_year_idx1 ON `academic_year` (`end_year`);
CREATE INDEX academic_year_archived_idx1 ON `academic_year` (`archived`);
CREATE INDEX academic_year_name_idx1 ON `academic_year` (`name`);
INSERT INTO `academic_year` (`start_month`, `start_year`, `end_month`, `end_year`)
VALUES 
('Aug', '2023', 'Jun', '2024'), ('Aug', '2024', 'Jun', '2025'),
('Aug', '2025', 'Jun', '2026'), ('Aug', '2026', 'Jun', '2027'),
('Aug', '2027', 'Jun', '2028'), ('Aug', '2028', 'Jun', '2029'),
('Aug', '2029', 'Jun', '2030'), ('Aug', '2030', 'Jun', '2031'),
('Aug', '2031', 'Jun', '2032'), ('Aug', '2032', 'Jun', '2033'),
('Aug', '2033', 'Jun', '2034'), ('Aug', '2034', 'Jun', '2035'),
('Aug', '2035', 'Jun', '2036'), ('Aug', '2036', 'Jun', '2037'),
('Aug', '2037', 'Jun', '2038'), ('Aug', '2038', 'Jun', '2039'),
('Aug', '2039', 'Jun', '2040'), ('Aug', '2040', 'Jun', '2041'),
('Aug', '2041', 'Jun', '2042'), ('Aug', '2042', 'Jun', '2043');

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
CREATE INDEX department_name_idx1 ON `department` (`name`);
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
  `fk_category` INT,
  `fk_department` INT,
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

INSERT INTO `course`(`code`, `name`, `credits`, `semester`, `level`, `fk_category`, `fk_department`) VALUES 
("BCME 407", "Digital Signal Processing", "3", "1", "400", "1", "1"),
("BEEE 409", "Linear Systems", "3", "1", "400", "1", "1"),
("BPSA 301", "Principles Of Management I", "3", "1", "400", "1", "1"),
("BCME 405", "Micro Processor Systems & Application", "3", "1", "400", "1", "1"),
("BCME 417", "Web Software Architecture (Elective)", "3", "1", "400", "1", "1"),
("BCME 400", "Project", "6", "1", "400", "1", "1"),

("BCME 401", "Artificial Intelligence", "3", "1", "400", "1", "1"),
("BCME 413", "E-Business/Commerce", "3", "1", "400", "1", "1"),
("BCME 409", "Project Work I", "3", "1", "400", "1", "1"),
("BCME 419", "Wireless and Mobile Computing", "3", "1", "400", "1", "1"),
("BINT 403", "Management Information System", "3", "1", "400", "1", "1"),

("BINT 401", "Mobile Computing", "3", "1", "400", "1", "1"),
("BINT 405", "Artificial Intelligence", "3", "1", "400", "1", "1"),
("BINT 407", "Wireless Technologies", "3", "1", "400", "1", "1"),
("BINT 400", "Project", "6", "1", "400", "1", "1"),
-- ("BCME 409", "Artificial Intelligence", "3", "1", "400", "1", "1"),
-- ("BPSA 301", "Principles of Management", "3", "1", "400", "1", "1"),
-- ("BINT 403", "Management Information Systems (MIS)", "3", "1", "400", "1", "1"),


("BCME 303", "Computer Communication Networks", "3", "1", "300", "1", "1"),
("BCME 309", "Design & Analysis Of Digital Systems", "3", "1", "300", "1", "1"),
("BCME 304", "Discrete Mathematics", "3", "1", "300", "1", "1"),
("BCME 311", "Digital Communication", "3", "1", "300", "1", "1"),
("BCME 305", "Operating Systems", "3", "1", "300", "1", "1"),
("BEEE 303", "Microprocessor Systems (Digital Electronics III)", "3", "1", "300", "1", "1"),
("BSMA 301", "Statistics & Probability", "3", "1", "300", "1", "1"),

("BINT 301", "Operating  Systems", "3", "1", "300", "1", "1"),
("BINT 303", "Database Systems I", "3", "1", "300", "1", "1"),
("BINT 305", "Data Communication & Computer Networks I", "3", "1", "300", "1", "1"),
("BINT 307", "Research Methods (Moved to 2nd Sem.)", "3", "1", "300", "1", "1"),
("BINT 309", "Business Intelligence System", "3", "1", "300", "1", "1"),
("BINT 311", "Programming With .Net", "3", "1", "300", "1", "1"),

("BCME 301", "Computer Communication Networks", "3", "1", "300", "1", "1"),
("BCME 302", "IT Project Management", "3", "1", "300", "1", "1"),
("BCME 306", "Introduction to Visual Basic", "3", "1", "300", "1", "1"),
("BCME 307", "Computer Architecture", "3", "1", "300", "1", "1"),
("BCME 310", "Operating Systems", "3", "1", "300", "1", "1"),
("BEME 311", "Formal Methods & Models", "3", "1", "300", "1", "1"),
("BCME 315", "Web Technologies", "3", "1", "300", "1", "1"),


("DITE 200", "Project ", "3", "1", "200", "1", "1"),
("DITE 201", "Systems Analysis and Design", "3", "1", "200", "1", "1"),
("DITE 203", "Object Oriented Programming (Java)", "3", "1", "200", "1", "1"),
("DITE 205", "Data Communication & Computer Networks I", "3", "1", "200", "1", "1"),
("DITE 207", "Operating Systems", "3", "1", "200", "1", "1"),
("DITE 209", "Computer Architecture", "3", "1", "200", "1", "1"),
("DITE 211", "Database Systems I", "3", "1", "200", "1", "1"),
("DITE 215", "Information Security", "3", "1", "200", "1", "1"),
("DITE 299", "Research Methods (Non-Examinable)", "3", "1", "200", "1", "1"),

("BCME 201", "Programming Language I (C++)", "3", "1", "200", "1", "1"),
("BCME 203", "Data Structures & Algorithms", "3", "1", "200", "1", "1"),
("BEEE 203", "Digital Electronics I (Combinational Logic)", "3", "1", "200", "1", "1"),
("BEEE 207", "Electronics II (Electronic Systems)", "3", "1", "200", "1", "1"),
("BMAE 205", "Strength Of Material Science", "3", "1", "200", "1", "1"),
("BMAE 207", "Thermodynamics I", "3", "1", "200", "1", "1"),
("BSMA 201", "Mathematics III (Calculus With Differential Equations)", "3", "1", "200", "1", "1"),

("BCME 213", "Database Management System", "3", "1", "200", "1", "1"),
("BCME 205", "Discrete Mathematics", "3", "1", "200", "1", "1"),
("BCME 207", "Object Oriented Programming With Java", "3", "1", "200", "1", "1"),
("BCME 209", "Digital Circuit Design II", "3", "1", "200", "1", "1"),
("BCME 211", "Data Structures And Algorithms", "3", "1", "200", "1", "1"),

("BACC 205", "Principles of Accounting I", "3", "1", "200", "1", "1"),
("BINT 201", "Systems Analysis & Design", "3", "1", "200", "1", "1"),
("BINT 203", "Object Oriented Programming (Principles)", "3", "1", "200", "1", "1"),
("BINT 207", "Introduction to Organizational Behaviour", "3", "1", "200", "1", "1"),
("BINT 209", "Computer Architecture", "3", "1", "200", "1", "1"),
("BINT 205", "Programming (WITH C++)", "3", "1", "200", "1", "1"),

-- ("BSMA 201", "Multivarialculus & Differential Equations", "3", "1", "200", "1", "1"),
-- ("BACC 205", "Principles of Accounting I", "3", "1", "200", "1", "1"),
-- ("BINT 201", "Systems Analysis & Design", "3", "1", "200", "1", "1"),
-- ("BINT 203", "Object Oriented Programming (Principles)", "3", "1", "200", "1", "1"),
-- ("BINT 205", "Programming (With C++)", "3", "1", "200", "1", "1"),
-- ("BINT 207", "Introduction to Organizational Behaviour", "3", "1", "200", "1", "1"),
-- ("BINT 209", "Computer Architecture", "3", "1", "200", "1", "1"),


("BEEE 101", "Applied Electricity", "3", "1", "100", "1", "1"),
("BMAE 101", "Basic Mechanics", "3", "1", "100", "1", "1"),
("BCME 101", "Computer Studies I (Intro To Computer Applications)", "3", "1", "100", "1", "1"),
("BMAE 103", "Engineering Drawing I", "3", "1", "100", "1", "1"),
("UFRE 103", "French I", "2", "1", "100", "1", "1"),
("BSMA 101", "Mathematics I (Algebra With Analysis)", "3", "1", "100", "1", "1"),
("BMAE 105", "Workshop Technology I", "3", "1", "100", "1", "1"),
("BMAE 107", "Material Science", "3", "1", "100", "1", "1"),
("UCOM 101", "Communication Skills I (Academic Writing Skills)", "2", "1", "100", "1", "1"),
("BCME 105", "Moral & Ethical Issues", "3", "1", "100", "1", "1"),
("UFRE 101", "French I", "2", "1", "100", "1", "1"),
("BPSA 101", "Introduction to Micro Economics", "3", "1", "100", "1", "1"),

("BINT 101", "Introduction To Computing", "3", "1", "100", "1", "1"),
("BINT 103", "Principles of Programming and Problem Solving", "3", "1", "100", "1", "1"),
("BINT 105", "Critical Thinking and Practical Reasoning", "3", "1", "100", "1", "1"),
("SBUS 105", "Principles of Management", "3", "1", "100", "1", "1"),

("DITE 101", "Introduction To Computing", "3", "1", "100", "1", "1"),
("DITE 103", "Principles of Programming and Problem Solving", "3", "1", "100", "1", "1"),
("DITE 105", "Critical Thinking and Practical Reasoning", "3", "1", "100", "1", "1")
;

-- ("BEEE 101", "Applied Electricity", "3", "1", "100", "1", "1"),
-- ("BCME 101", "Computer Studies I (Intro. to Computer Applications)", "3", "1", "100", "1", "1"),
-- ("BSMA 101", "Mathematics I (Algebra With Analysis)", "3", "1", "100", "1", "1"),
-- ("UCOM 101", "Communication Skills I", "2", "1", "100", "1", "1"),
-- ("UFRE 103", "French I", "2", "1", "100", "1", "1"),
-- ("BSMA 101", "Calculus I", "3", "1", "100", "1", "1"),
-- ("UCOM 101", "Communication Skills", "2", "1", "100", "1", "1"),
-- ("UFRE 103", "French I", "2", "1", "100", "1", "1"),
-- ("BSMA 101", "Calculus I", "3", "1", "100", "1", "1")
-- ("SBUS 105", "Principles of Management", "3", "1", "100", "1", "1"),



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
CREATE INDEX room_capacity_idx1 ON `room` (`capacity`);
CREATE INDEX room_location_idx1 ON `room` (`location`);
CREATE INDEX room_longitude_idx1 ON `room` (`longitude`);
CREATE INDEX room_latitude_idx1 ON `room` (`latitude`);
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
ALTER TABLE `course_registration` 
ADD COLUMN `registered` TINYINT(1) DEFAULT 0 AFTER `fk_semester`,
ADD COLUMN `fk_semester_registered` INT AFTER `fk_semester`,
ADD COLUMN `semester` INT AFTER `fk_semester_registered`,
ADD COLUMN `level` INT AFTER `fk_semester_registered`,
ADD CONSTRAINT `fk_course_registration_semester2` FOREIGN KEY (`fk_semester_registered`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
CREATE INDEX course_registration_archived_idx1 ON `course_registration` (`archived`);
CREATE INDEX course_registration_registered_idx1 ON `course_registration` (`registered`);
CREATE INDEX course_registration_semester_idx1 ON `course_registration` (`semester`);
CREATE INDEX course_registration_level_idx1 ON `course_registration` (`level`);

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

ALTER TABLE `course` ADD CONSTRAINT `fk_course_category1` FOREIGN KEY (`fk_category`) REFERENCES `course_category`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE; ALTER TABLE `course` ADD CONSTRAINT `fk_course_department1` FOREIGN KEY (`fk_department`) REFERENCES `department`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE; 
ALTER TABLE `form_sections_chek` ADD COLUMN `programme_duration` INT DEFAULT NULL AFTER `programme_awarded`;
ALTER TABLE `form_sections_chek` ADD COLUMN `level_admitted` INT DEFAULT NULL AFTER `programme_duration`;
ALTER TABLE `student` ADD COLUMN `programme_duration` INT DEFAULT 4 AFTER `level_admitted`;
CREATE INDEX student_programme_duration_idx1 ON `student` (`programme_duration`);

ALTER TABLE `section` 
ADD COLUMN `credits` INT NOT NULL AFTER `fk_course`,
ADD COLUMN `level` INT NOT NULL AFTER `credits`,
ADD COLUMN `semester` INT NOT NULL AFTER `level`;
CREATE INDEX section_credits_idx1 ON section (`credits`);
CREATE INDEX section_level_idx1 ON section (`level`);
CREATE INDEX section_semester_idx1 ON section (`semester`);



CREATE INDEX form_sections_chek_personal_idx1 ON `form_sections_chek` (`personal`);
CREATE INDEX form_sections_chek_education_idx1 ON `form_sections_chek` (`education`);
CREATE INDEX form_sections_chek_programme_idx1 ON `form_sections_chek` (`programme`);
CREATE INDEX form_sections_chek_uploads_idx1 ON `form_sections_chek` (`uploads`);
CREATE INDEX form_sections_chek_declaration_idx1 ON `form_sections_chek` (`declaration`);
CREATE INDEX form_sections_chek_reviewed_idx1 ON `form_sections_chek` (`reviewed`);
CREATE INDEX form_sections_chek_admitted_idx1 ON `form_sections_chek` (`admitted`);
CREATE INDEX form_sections_chek_declined_idx1 ON `form_sections_chek` (`declined`);
CREATE INDEX form_sections_chek_enrolled_idx1 ON `form_sections_chek` (`enrolled`);
CREATE INDEX form_sections_chek_printed_idx1 ON `form_sections_chek` (`printed`);
CREATE INDEX form_sections_chek_notified_sms_idx1 ON `form_sections_chek` (`notified_sms`);
CREATE INDEX form_sections_chek_emailed_letter_idx1 ON `form_sections_chek` (`emailed_letter`);
CREATE INDEX form_sections_chek_programme_awarded_idx1 ON `form_sections_chek` (`programme_awarded`);
CREATE INDEX form_sections_chek_programme_duration_idx1 ON `form_sections_chek` (`programme_duration`);
CREATE INDEX form_sections_chek_level_admitted_idx1 ON `form_sections_chek` (`level_admitted`);

CREATE INDEX purchase_detail_sold_by_idx1 ON `purchase_detail` (`sold_by`);
CREATE INDEX purchase_detail_ext_trans_datetime_idx1 ON `purchase_detail` (`ext_trans_datetime`);
CREATE INDEX purchase_detail_first_name_idx1 ON `purchase_detail` (`first_name`);
CREATE INDEX purchase_detail_last_name_idx1 ON `purchase_detail` (`last_name`);
CREATE INDEX purchase_detail_email_address_idx1 ON `purchase_detail` (`email_address`);
CREATE INDEX purchase_detail_country_name_idx1 ON `purchase_detail` (`country_name`);
CREATE INDEX purchase_detail_country_code_idx1 ON `purchase_detail` (`country_code`);
CREATE INDEX purchase_detail_phone_number_idx1 ON `purchase_detail` (`phone_number`);
CREATE INDEX purchase_detail_amount_idx1 ON `purchase_detail` (`amount`);
CREATE INDEX purchase_detail_app_number_idx1 ON `purchase_detail` (`app_number`);
CREATE INDEX purchase_detail_pin_number_idx1 ON `purchase_detail` (`pin_number`);
CREATE INDEX purchase_detail_status_idx1 ON `purchase_detail` (`status`);
CREATE INDEX purchase_detail_added_at_idx1 ON `purchase_detail` (`added_at`);
CREATE INDEX purchase_detail_payment_method_idx1 ON `purchase_detail` (`payment_method`);
CREATE INDEX purchase_detail_deleted_idx1 ON `purchase_detail` (`deleted`);
CREATE INDEX purchase_detail_sms_sent_idx1 ON `purchase_detail` (`sms_sent`);
CREATE INDEX purchase_detail_email_sent_idx1 ON `purchase_detail` (`email_sent`);

ALTER TABLE forms 
ADD COLUMN dollar_cedis_rate DECIMAL(5,2) DEFAULT 10 AFTER amount,
ADD COLUMN member_amount DECIMAL(5,2) GENERATED ALWAYS AS (amount / dollar_cedis_rate) STORED AFTER dollar_cedis_rate,
ADD COLUMN non_member_amount DECIMAL(5,2) DEFAULT 50 AFTER amount;