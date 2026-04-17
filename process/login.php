<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $errors = [];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($pass)) $errors[] = "Password is required.";

    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        header("Location: ../auth.php#login");
        exit;
    }

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['success_message'] = "Welcome back, " . $user['full_name'] . "!";
        header("Location: ../history.php");
        exit;
    } else {
        $_SESSION['login_errors'] = ["Invalid email or password. Please try again."];
        header("Location: ../auth.php#login");
        exit;
    }
}

header("Location: ../auth.php");
exit;
?>
