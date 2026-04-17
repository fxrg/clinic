-- ============================================================
-- clinic_db.sql — Manual Database Setup Script
-- DS362 Clinic Appointment System
--
-- Instructions:
--   1. Open phpMyAdmin or MySQL Workbench
--   2. Run this entire file
--   3. The database, tables, and demo data will be created
-- ============================================================

-- Create and select the database
CREATE DATABASE IF NOT EXISTS clinic_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE clinic_db;

-- ── Table: users ──────────────────────────────────────────────
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table: appointments ───────────────────────────────────────
CREATE TABLE appointments (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    user_id          INT          NOT NULL,
    doctor_name      VARCHAR(100) NOT NULL,
    specialty        VARCHAR(100) NOT NULL,
    appointment_date DATE         NOT NULL,
    appointment_time TIME         NOT NULL,
    notes            TEXT,
    status           ENUM('Pending','Confirmed','Completed','Cancelled')
                     NOT NULL DEFAULT 'Pending',
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Demo Data: Users ──────────────────────────────────────────
-- Password for demo user: Password123
-- Hash generated with PHP: password_hash('Password123', PASSWORD_DEFAULT)
INSERT INTO users (full_name, email, password) VALUES
(
    'Demo Patient',
    'demo@clinic.edu',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);

-- ── Demo Data: Appointments ───────────────────────────────────
INSERT INTO appointments (user_id, doctor_name, specialty, appointment_date, appointment_time, notes, status) VALUES
(1, 'Dr. Abdullah Al-Mutairi', 'Cardiovascular Science', '2026-04-24', '10:30:00', 'Follow-up on stress test results', 'Confirmed'),
(1, 'Dr. Sarah Al-Otaibi',     'Neurological Disorders', '2026-04-28', '14:15:00', 'Recurring migraine evaluation',   'Pending'),
(1, 'Dr. Khalid Al-Anazi',     'Endocrinology',          '2026-03-14', '09:00:00', 'Annual hormone panel check',       'Completed'),
(1, 'Dr. Ahmad Al-Harbi',      'Pediatric Surgery',      '2026-02-02', '11:15:00', 'Post-surgery consultation',        'Completed');

-- ── Indexes ───────────────────────────────────────────────────
CREATE INDEX idx_appointments_user_id ON appointments (user_id);
CREATE INDEX idx_appointments_date    ON appointments (appointment_date);
CREATE INDEX idx_users_email          ON users (email);
