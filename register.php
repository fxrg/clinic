<?php
/**
 * register.php — User Registration Handler
 * DS362 Clinic Appointment System
 *
 * Receives POST from auth.html registration form.
 * Validates inputs server-side, inserts user into DB,
 * then redirects back to auth.html (with errors) or history.php (success).
 */

require_once 'db.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'register') {
    header('Location: auth.html');
    exit;
}

// ── Collect & sanitize input ──────────────────────────────────
$full_name       = trim($_POST['full_name']       ?? '');
$email           = trim($_POST['email']           ?? '');
$password        = $_POST['password']             ?? '';
$confirm_password= $_POST['confirm_password']     ?? '';

// ── Server-side validation ────────────────────────────────────
$errors = [];

if (strlen($full_name) < 2) {
    $errors[] = 'Full name must be at least 2 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Password must contain at least one uppercase letter.';
}
if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match.';
}

// ── Redirect back if validation failed ───────────────────────
if (!empty($errors)) {
    $encoded = urlencode(json_encode($errors));
    header("Location: auth.html?register_errors=$encoded#register");
    exit;
}

// ── Check if email already exists (SELECT) ────────────────────
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $encoded = urlencode(json_encode(['This email is already registered. Please login instead.']));
    header("Location: auth.html?register_errors=$encoded#login");
    exit;
}
$stmt->close();

// ── Hash password and INSERT user ─────────────────────────────
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $full_name, $email, $hashed);

if ($stmt->execute()) {
    // ── Set session and redirect to history ───────────────────
    $_SESSION['user_id']   = $conn->insert_id;
    $_SESSION['user_name'] = $full_name;
    $_SESSION['flash']     = 'Welcome, ' . htmlspecialchars($full_name) . '! Your account has been created.';
    $stmt->close();
    header('Location: history.php');
    exit;
} else {
    $stmt->close();
    $encoded = urlencode(json_encode(['Registration failed. Please try again.']));
    header("Location: auth.html?register_errors=$encoded#register");
    exit;
}
?>
