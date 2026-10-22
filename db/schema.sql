-- ============================================================
-- Web Technology Assignment 2: Student Management Database
-- Database Schema: student_portal
-- Includes tables for:
--  1. users (Session authentication for Admin & Servlet Portal)
--  2. students (Core student records: name, roll number, email, course)
--  3. student_documents (File metadata: path, type, size, upload date)
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- Drop tables in reverse order of foreign keys to avoid conflicts
DROP TABLE IF EXISTS student_documents;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;

-- ------------------------------------------------------------
-- Table: users
-- Used for Session Authentication in Question 3 (Servlet Portal)
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed default portal login credentials
-- Default credentials: username = 'admin', password = 'password123'
INSERT INTO users (username, password, role) VALUES 
('admin', 'password123', 'admin'),
('faculty', 'faculty123', 'faculty');

-- ------------------------------------------------------------
-- Table: students
-- Stores primary student profile details
-- ------------------------------------------------------------
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(50) NOT NULL,
    photo VARCHAR(255) DEFAULT 'default_avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table: student_documents
-- Stores metadata of uploaded documents (ID proof, certificates, etc.)
-- Links to students via foreign key with ON DELETE CASCADE
-- ------------------------------------------------------------
CREATE TABLE student_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL, -- in bytes
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_doc
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Sample Records for immediate testing & demonstration
-- ------------------------------------------------------------
INSERT INTO students (id, name, roll_no, email, course, photo) VALUES
(1, 'Aarav Sharma', 'CS202601', 'aarav.sharma@college.edu', 'Computer Science', 'student_1_photo.png'),
(2, 'Priya Patel', 'IT202602', 'priya.patel@college.edu', 'Information Technology', 'student_2_photo.png'),
(3, 'Muthu Saravanan', 'EC202603', 'muthu.s@college.edu', 'Electronics & Comm.', 'student_3_photo.png');

INSERT INTO student_documents (student_id, file_name, file_path, file_type, file_size, upload_date) VALUES
(1, 'Aadhaar_ID_Proof.pdf', 'uploads/doc_1_id.pdf', 'application/pdf', 154200, NOW()),
(1, 'HSC_MarkSheet.pdf', 'uploads/doc_1_cert.pdf', 'application/pdf', 241000, NOW()),
(2, 'Passport_Copy.pdf', 'uploads/doc_2_passport.pdf', 'application/pdf', 189400, NOW()),
(3, 'Transfer_Certificate.pdf', 'uploads/doc_3_tc.pdf', 'application/pdf', 132000, NOW());
