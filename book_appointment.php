<?php
/**
 * book_appointment.php — Appointment Booking Handler
 * DS362 Clinic Appointment System
 *
 * Receives POST from book.html booking form.
 * Checks session, validates input, INSERTs appointment into DB,
 * redirects to history.php on success or book.html on error.
 */

require_once 'db.php';

// ── Authentication check ──────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    $encoded = urlencode(json_encode(['You must be logged in to book an appointment.']));
    header("Location: auth.html?login_errors=$encoded#login");
    exit;
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'book') {
    header('Location: book.html');
    exit;
}

// ── Collect & sanitize input ──────────────────────────────────
$user_id    = (int)$_SESSION['user_id'];
$doctor     = trim($_POST['doctor_name']       ?? '');
$specialty  = trim($_POST['specialty']         ?? '');
$date       = trim($_POST['appointment_date']  ?? '');
$time       = trim($_POST['appointment_time']  ?? '');
$notes      = trim($_POST['notes']             ?? '');
$today      = date('Y-m-d');

// ── Server-side validation ────────────────────────────────────
$errors = [];

if (empty($doctor)) {
    $errors[] = 'Please select a doctor.';
}
if (empty($date)) {
    $errors[] = 'Appointment date is required.';
} elseif ($date < $today) {
    $errors[] = 'Appointment date cannot be in the past.';
}
if (empty($time)) {
    $errors[] = 'Please select an appointment time.';
}

if (!empty($errors)) {
    $encoded = urlencode(json_encode($errors));
    header("Location: book.html?book_errors=$encoded");
    exit;
}

// ── INSERT appointment into database ──────────────────────────
$stmt = $conn->prepare("
    INSERT INTO appointments
        (user_id, doctor_name, specialty, appointment_date, appointment_time, notes, status)
    VALUES
        (?, ?, ?, ?, ?, ?, 'Pending')
");
$stmt->bind_param('isssss', $user_id, $doctor, $specialty, $date, $time, $notes);

if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['flash'] = 'Your appointment with ' . htmlspecialchars($doctor) . ' has been booked successfully!';
    header('Location: history.php');
    exit;
} else {
    $stmt->close();
    $encoded = urlencode(json_encode(['Booking failed. Please try again.']));
    header("Location: book.html?book_errors=$encoded");
    exit;
}
?>
