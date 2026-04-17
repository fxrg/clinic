<?php
/**
 * logout.php — Logout Handler
 * DS362 Clinic Appointment System
 *
 * Destroys the session and redirects to the home page.
 */

require_once 'db.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: index.html');
exit;
?>
