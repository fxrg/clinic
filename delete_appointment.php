<?php
/**
 * delete_appointment.php — Delete Appointment Handler
 * DS362 Clinic Appointment System
 *
 * Receives POST from history.php delete confirmation modal.
 * Authentication check → DELETE record from DB (user-scoped).
 */

require_once 'db.php';

// ── Authentication check ──────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.html#login');
    exit;
}

// Only handle POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'delete') {
    header('Location: history.php');
    exit;
}

// ── Collect input ─────────────────────────────────────────────
$appt_id = (int)($_POST['appointment_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

if ($appt_id <= 0) {
    $_SESSION['flash_error'] = 'Invalid appointment ID.';
    header('Location: history.php');
    exit;
}

// ── DELETE appointment from database ──────────────────────────
// WHERE clause includes user_id so patients can only delete their own appointments
$stmt = $conn->prepare("
    DELETE FROM appointments
    WHERE id      = ?
      AND user_id = ?
");
$stmt->bind_param('ii', $appt_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['flash'] = 'Appointment cancelled and removed from your records.';
} else {
    $_SESSION['flash_error'] = 'Appointment not found or you are not authorized to delete it.';
}

$stmt->close();
header('Location: history.php');
exit;
?>
