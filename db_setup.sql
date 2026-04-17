-- DS362 Clinic Appointment System - Database Setup Script
-- Run this in phpMyAdmin or MySQL CLI if db.php auto-setup doesn't work

CREATE DATABASE IF NOT EXISTS clinic_db;
USE clinic_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    notes TEXT,
    status ENUM('Confirmed','Pending','Cancelled','Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample doctor data (used as reference for form dropdown)
-- In this system, doctors are hardcoded in the UI (no separate doctors table required for the 1-2 table constraint)

-- Sample user (password is: Password123)
INSERT IGNORE INTO users (full_name, email, password) VALUES 
('Demo Patient', 'demo@clinic.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample appointments for demo user (id=1)
INSERT IGNORE INTO appointments (user_id, doctor_name, specialty, appointment_date, appointment_time, notes, status) VALUES
(1, 'Dr. Alistair Thorne', 'Cardiovascular Science', '2026-04-24', '10:30:00', 'Follow-up on stress test results', 'Confirmed'),
(1, 'Dr. Elena Rodriguez', 'Neurological Disorders', '2026-04-28', '14:15:00', 'Headache evaluation', 'Pending'),
(1, 'Dr. Sarah Jenkins', 'Endocrinology', '2026-03-14', '09:00:00', 'Annual hormone check', 'Completed'),
(1, 'Dr. Julian Vance', 'Pediatric Surgery', '2026-02-02', '11:15:00', 'Consultation follow-up', 'Completed');
