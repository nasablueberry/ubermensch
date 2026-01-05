-- Migration: create_tables.sql
-- Run in MySQL for database `ubermench`

DROP TABLE IF EXISTS RegionalAidStats;
DROP TABLE IF EXISTS DuplicateAidLog;
DROP TABLE IF EXISTS EligibilityCheck;
DROP TABLE IF EXISTS Disbursement;
DROP TABLE IF EXISTS Application;
DROP TABLE IF EXISTS ScholarshipProgram;
DROP TABLE IF EXISTS AidProvider;
DROP TABLE IF EXISTS Student;
DROP TABLE IF EXISTS Institution;
DROP TABLE IF EXISTS Region;

CREATE TABLE Region (
  region_id INT AUTO_INCREMENT PRIMARY KEY,
  district VARCHAR(128) NOT NULL,
  upazila VARCHAR(128) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE Institution (
  institution_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  type VARCHAR(64),
  district VARCHAR(128),
  upazila VARCHAR(128)
) ENGINE=InnoDB;

CREATE TABLE Student (
  student_id VARCHAR(64) PRIMARY KEY,
  birth_certificate_id VARCHAR(128) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  dob DATE,
  gender VARCHAR(16),
  household_income DECIMAL(12,2),
  cgpa DECIMAL(4,2),
  attendance_percentage DECIMAL(5,2),
  address TEXT,
  district VARCHAR(128),
  upazila VARCHAR(128),
  verification_status VARCHAR(32) DEFAULT 'unverified',
  password VARCHAR(255) NOT NULL,
  institution_id INT,
  region_id INT,
  FOREIGN KEY (institution_id) REFERENCES Institution(institution_id) ON DELETE SET NULL,
  FOREIGN KEY (region_id) REFERENCES Region(region_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE AidProvider (
  provider_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  provider_type VARCHAR(64),
  contact_email VARCHAR(255),
  contact_phone VARCHAR(64),
  address TEXT
) ENGINE=InnoDB;

CREATE TABLE Admin (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE ScholarshipProgram (
  program_id INT AUTO_INCREMENT PRIMARY KEY,
  program_name VARCHAR(255) NOT NULL,
  description TEXT,
  eligibility_income_threshold DECIMAL(12,2),
  eligibility_cgpa_threshold DECIMAL(4,2),
  total_funds DECIMAL(14,2) DEFAULT 0,
  funds_remaining DECIMAL(14,2) DEFAULT 0,
  start_date DATE,
  end_date DATE,
  provider_id INT,
  FOREIGN KEY (provider_id) REFERENCES AidProvider(provider_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE Application (
  application_id INT AUTO_INCREMENT PRIMARY KEY,
  application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(32) DEFAULT 'pending',
  review_date DATETIME NULL,
  comments TEXT,
  student_id VARCHAR(64),
  program_id INT,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
  FOREIGN KEY (program_id) REFERENCES ScholarshipProgram(program_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Disbursement (
  disbursement_id INT AUTO_INCREMENT PRIMARY KEY,
  amount_released DECIMAL(14,2) NOT NULL,
  release_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  payment_method VARCHAR(64),
  transaction_reference VARCHAR(255),
  application_id INT,
  FOREIGN KEY (application_id) REFERENCES Application(application_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE EligibilityCheck (
  check_id INT AUTO_INCREMENT PRIMARY KEY,
  income_ok TINYINT(1),
  cgpa_ok TINYINT(1),
  overall_eligible TINYINT(1),
  check_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  student_id VARCHAR(64),
  program_id INT,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
  FOREIGN KEY (program_id) REFERENCES ScholarshipProgram(program_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE DuplicateAidLog (
  duplicate_id INT AUTO_INCREMENT PRIMARY KEY,
  detection_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  notes TEXT,
  student_id VARCHAR(64),
  conflicting_program_id INT,
  FOREIGN KEY (student_id) REFERENCES Student(student_id) ON DELETE CASCADE,
  FOREIGN KEY (conflicting_program_id) REFERENCES ScholarshipProgram(program_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE RegionalAidStats (
  stats_id INT AUTO_INCREMENT PRIMARY KEY,
  total_students_supported INT DEFAULT 0,
  total_funds_disbursed DECIMAL(14,2) DEFAULT 0,
  reporting_period VARCHAR(64),
  region_id INT,
  FOREIGN KEY (region_id) REFERENCES Region(region_id) ON DELETE CASCADE
) ENGINE=InnoDB;
