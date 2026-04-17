<?php
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth.php#login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $appt_id = (int)($_POST['appointment_id'] ?? 0);
    $user_id = (int)$_SESSION['user_id'];

    if ($appt_id > 0) {
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $appt_id, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Appointment cancelled and removed.";
        } else {
            $_SESSION['error_message'] = "Appointment not found or not authorized.";
        }
        $stmt->close();
    }
}

header("Location: ../history.php");
exit;
?>
