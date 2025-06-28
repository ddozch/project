<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit();
    }
}

function is_admin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
