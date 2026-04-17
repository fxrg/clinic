<?php
/**
 * login.php — User Login Handler
 * DS362 Clinic Appointment System
 *
 * Receives POST from auth.html login form.
 * Validates credentials against DB (SELECT), sets session,
 * redirects to history.php on success or auth.html on failure.
 */

require_once 'db.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'login') {
    header('Location: auth.html');
    exit;
}

// ── Collect input ─────────────────────────────────────────────
$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';

// ── Server-side validation ────────────────────────────────────
$errors = [];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if (empty($password)) {
    $errors[] = 'Password cannot be empty.';
}

if (!empty($errors)) {
    $encoded = urlencode(json_encode($errors));
    header("Location: auth.html?login_errors=$encoded#login");
    exit;
}

// ── SELECT user from database ─────────────────────────────────
$stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// ── Verify password ───────────────────────────────────────────
if ($user && password_verify($password, $user['password'])) {
    // Login successful — set session variables
    $_SESSION['user_id']   = (int)$user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['flash']     = 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!';
    header('Location: history.php');
    exit;
} else {
    // Invalid credentials
    $encoded = urlencode(json_encode(['Invalid email or password. Please try again.']));
    header("Location: auth.html?login_errors=$encoded#login");
    exit;
}
?>
