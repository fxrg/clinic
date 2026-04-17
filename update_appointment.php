<?php
/**
 * update_appointment.php — Reschedule Appointment Handler
 * DS362 Clinic Appointment System
 *
 * Receives POST from history.php reschedule modal form.
 * Authentication check → validation → UPDATE record in DB.
 */

require_once 'db.php';

// ── Authentication check ──────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.html#login');
    exit;
}

// Only handle POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'update') {
    header('Location: history.php');
    exit;
}

// ── Collect input ─────────────────────────────────────────────
$appt_id    = (int)($_POST['appointment_id'] ?? 0);
$user_id    = (int)$_SESSION['user_id'];
$new_date   = trim($_POST['new_date'] ?? '');
$new_time   = trim($_POST['new_time'] ?? '');
$new_status = 'Confirmed'; // Rescheduled appointments are set to Confirmed

// ── Validation ────────────────────────────────────────────────
$today = date('Y-m-d');

if ($appt_id <= 0 || empty($new_date) || empty($new_time)) {
    $_SESSION['flash_error'] = 'Invalid reschedule data. Please try again.';
    header('Location: history.php');
    exit;
}

if ($new_date < $today) {
    $_SESSION['flash_error'] = 'Reschedule date cannot be in the past.';
    header('Location: history.php');
    exit;
}

// ── UPDATE appointment in database ────────────────────────────
// WHERE clause includes user_id to prevent unauthorized updates
$stmt = $conn->prepare("
    UPDATE appointments
    SET appointment_date = ?,
        appointment_time = ?,
        status           = ?
    WHERE id      = ?
      AND user_id = ?
");
$stmt->bind_param('sssii', $new_date, $new_time, $new_status, $appt_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['flash'] = 'Appointment rescheduled successfully.';
} else {
    $_SESSION['flash_error'] = 'Could not reschedule. The appointment may not exist.';
}

$stmt->close();
header('Location: history.php');
exit;
?>
