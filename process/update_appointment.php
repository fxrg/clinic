<?php
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth.php#login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $appt_id = (int)($_POST['appointment_id'] ?? 0);
    $user_id = (int)$_SESSION['user_id'];
    $new_date = trim($_POST['new_date'] ?? '');
    $new_time = trim($_POST['new_time'] ?? '');
    $new_status = trim($_POST['new_status'] ?? 'Pending');

    $allowed_statuses = ['Confirmed', 'Pending', 'Cancelled', 'Completed'];
    if (!in_array($new_status, $allowed_statuses)) $new_status = 'Pending';

    if ($appt_id > 0 && !empty($new_date) && !empty($new_time)) {
        $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ?, status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $new_date, $new_time, $new_status, $appt_id, $user_id);
        $stmt->execute();
        $_SESSION['success_message'] = "Appointment rescheduled successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update. Please provide a valid date and time.";
    }
}

header("Location: ../history.php");
exit;
?>
