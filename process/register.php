<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $name  = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($name)) $errors[] = "Full name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (strlen($pass) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($pass !== $confirm) $errors[] = "Passwords do not match.";

    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_input'] = ['full_name' => $name, 'email' => $email];
        header("Location: ../auth.php#register");
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_errors'] = ["This email is already registered. Please login."];
        header("Location: ../auth.php#register");
        exit;
    }
    $stmt->close();

    // Insert user
    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['success_message'] = "Welcome, $name! Your account has been created.";
        header("Location: ../history.php");
        exit;
    } else {
        $_SESSION['register_errors'] = ["Registration failed. Please try again."];
        header("Location: ../auth.php#register");
        exit;
    }
}

header("Location: ../auth.php");
exit;
?>
