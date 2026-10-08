-- =============================================================================
-- KPR Institute of Engineering and Technology
-- Department of Computer Science and Engineering
-- Course: U21CS501 - Web Technology | Assignment II
-- Database Schema Script: web_tech_db
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `web_tech_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `web_tech_db`;

-- -----------------------------------------------------------------------------
-- 1. Users Table (For Servlet & PHP Session Authentication)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, -- Stored as bcrypt hash or plain text for lab testing
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'staff', 'student') DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin login credentials (username: admin, password: admin123password)
INSERT INTO `users` (`username`, `password`, `full_name`, `role`) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1H8qW1qJ5m3L6Yw1B.zK9Q5m1X1K1K2', 'System Administrator', 'admin'),
('prof_kavin', 'kavin2026', 'Prof. Kavin Kumar', 'staff');


-- -----------------------------------------------------------------------------
-- 2. Students Table (For Admission & Profile Management)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `student_id` INT AUTO_INCREMENT PRIMARY KEY,
    `roll_number` VARCHAR(20) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `course` VARCHAR(100) NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
    `dob` DATE DEFAULT NULL,
    `phone` VARCHAR(15) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `admission_status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Approved',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- -----------------------------------------------------------------------------
-- 3. Student Documents Metadata Table (Stores file metadata & paths)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `student_documents`;
CREATE TABLE `student_documents` (
    `document_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `document_type` ENUM('profile_image', 'id_proof', 'certificate') NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL UNIQUE, -- Unique stored filename (e.g. uuid_filename)
    `file_path` VARCHAR(500) NOT NULL,       -- Absolute or relative disk storage path
    `file_type` VARCHAR(50) NOT NULL,        -- MIME type e.g. image/png, application/pdf
    `file_size` INT NOT NULL,                -- File size in bytes
    `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- -----------------------------------------------------------------------------
-- 4. Sample Seed Data for Testing
-- -----------------------------------------------------------------------------
INSERT INTO `students` (`student_id`, `roll_number`, `full_name`, `email`, `course`, `gender`, `dob`, `phone`, `address`, `admission_status`) VALUES
(1, '7377211CS101', 'Aravind Swamy', 'aravind.s@kpriet.ac.in', 'B.E. Computer Science and Engineering', 'Male', '2004-05-15', '9876543210', '123 Cross Street, Coimbatore', 'Approved'),
(2, '7377211CS102', 'Bhavana Ramesh', 'bhavana.r@kpriet.ac.in', 'B.Tech Information Technology', 'Female', '2004-08-22', '9876543211', '45 North Avenue, Tiruppur', 'Approved'),
(3, '7377211CS103', 'Chandru Mohan', 'chandru.m@kpriet.ac.in', 'B.E. Artificial Intelligence & Data Science', 'Male', '2004-12-10', '9876543212', '78 South Bypass, Erode', 'Pending');

INSERT INTO `student_documents` (`document_id`, `student_id`, `document_type`, `original_name`, `file_name`, `file_path`, `file_type`, `file_size`, `upload_date`) VALUES
(1, 1, 'profile_image', 'aravind_photo.jpg', 'profile_1_aravind.jpg', 'uploads/profile_1_aravind.jpg', 'image/jpeg', 245000, CURRENT_TIMESTAMP),
(2, 1, 'id_proof', 'aadhaar_aravind.pdf', 'doc_1_aadhaar.pdf', 'uploads/doc_1_aadhaar.pdf', 'application/pdf', 512000, CURRENT_TIMESTAMP),
(3, 2, 'profile_image', 'bhavana_photo.png', 'profile_2_bhavana.png', 'uploads/profile_2_bhavana.png', 'image/png', 189000, CURRENT_TIMESTAMP),
(4, 3, 'profile_image', 'chandru_photo.jpg', 'profile_3_chandru.jpg', 'uploads/profile_3_chandru.jpg', 'image/jpeg', 310000, CURRENT_TIMESTAMP);
