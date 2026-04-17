<?php
/**
 * db.php — Database Connection & Auto-Setup
 * DS362 Clinic Appointment System
 *
 * - Connects to MySQL
 * - Creates the database if it doesn't exist
 * - Creates the users and appointments tables if they don't exist
 * - Starts the session
 */

// ── Database credentials ───────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // ← Change to your MySQL username
define('DB_PASS', '');         // ← Change to your MySQL password
define('DB_NAME', 'clinic_db');

// ── Start session once ─────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Connect to MySQL (no database selected yet) ────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// ── Create database if not exists ──────────────────────────────
$conn->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '`');
$conn->select_db(DB_NAME);

// ── Create users table ─────────────────────────────────────────
$conn->query("
    CREATE TABLE IF NOT EXISTS users (
        id         INT          AUTO_INCREMENT PRIMARY KEY,
        full_name  VARCHAR(100) NOT NULL,
        email      VARCHAR(150) NOT NULL UNIQUE,
        password   VARCHAR(255) NOT NULL,
        created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    )
");

// ── Create appointments table ──────────────────────────────────
$conn->query("
    CREATE TABLE IF NOT EXISTS appointments (
        id               INT          AUTO_INCREMENT PRIMARY KEY,
        user_id          INT          NOT NULL,
        doctor_name      VARCHAR(100) NOT NULL,
        specialty        VARCHAR(100) NOT NULL,
        appointment_date DATE         NOT NULL,
        appointment_time TIME         NOT NULL,
        notes            TEXT,
        status           ENUM('Pending','Confirmed','Completed','Cancelled')
                         DEFAULT 'Pending',
        created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

// ── Insert demo user if table is empty ────────────────────────
$result = $conn->query("SELECT COUNT(*) AS cnt FROM users");
$row    = $result->fetch_assoc();
if ((int)$row['cnt'] === 0) {
    // Demo credentials: demo@clinic.edu / Password123
    $hashed = password_hash('Password123', PASSWORD_DEFAULT);
    $conn->query("
        INSERT INTO users (full_name, email, password)
        VALUES ('Demo Patient', 'demo@clinic.edu', '$hashed')
    ");
    $demoId = $conn->insert_id;

    // Insert sample appointments for the demo user
    $conn->query("
        INSERT INTO appointments
            (user_id, doctor_name, specialty, appointment_date, appointment_time, notes, status)
        VALUES
            ($demoId, 'Dr. Alistair Thorne', 'Cardiovascular Science',
             '2026-04-24', '10:30:00', 'Follow-up on stress test', 'Confirmed'),
            ($demoId, 'Dr. Elena Rodriguez', 'Neurological Disorders',
             '2026-04-28', '14:15:00', 'Headache evaluation', 'Pending'),
            ($demoId, 'Dr. Sarah Jenkins', 'Endocrinology',
             '2026-03-14', '09:00:00', 'Annual hormone check', 'Completed'),
            ($demoId, 'Dr. Julian Vance', 'Pediatric Surgery',
             '2026-02-02', '11:15:00', 'Consultation follow-up', 'Completed')
    ");
}
?>
