<?php
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth.php#login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book') {
    $user_id  = (int)$_SESSION['user_id'];
    $doctor   = trim($_POST['doctor_name'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $date     = trim($_POST['appointment_date'] ?? '');
    $time     = trim($_POST['appointment_time'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');

    $errors = [];

    if (empty($doctor)) $errors[] = "Please select a doctor.";
    if (empty($date)) $errors[] = "Appointment date is required.";
    elseif (strtotime($date) < strtotime(date('Y-m-d'))) $errors[] = "Appointment date cannot be in the past.";
    if (empty($time)) $errors[] = "Appointment time is required.";

    if (!empty($errors)) {
        $_SESSION['book_errors'] = $errors;
        $_SESSION['book_input'] = compact('doctor', 'specialty', 'date', 'time', 'notes');
        header("Location: ../book.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_name, specialty, appointment_date, appointment_time, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isssss", $user_id, $doctor, $specialty, $date, $time, $notes);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Your appointment has been booked successfully!";
        header("Location: ../history.php");
        exit;
    } else {
        $_SESSION['book_errors'] = ["Booking failed. Please try again."];
        header("Location: ../book.php");
        exit;
    }
}

header("Location: ../book.php");
exit;
?>
