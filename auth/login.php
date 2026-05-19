<?php
session_start();
require_once "../includes/connection.php";

// Only accepts POST method
if ($_SERVER["REQUEST_METHOD"] !== "POST"){
    header('Location: ../pages/login.php');
    exit();
}

// Collect data
$email = trim($_POST["email"] ?? "");
$password = ($_POST["password"] ?? "");

// Validation
if ($email === "" || $password === "") {
    $_SESSION['login_error'] = "Please enter your email and password.";
    header('Location: ../pages/login.php?tab=login');
    exit();
}

// Check if user exists 
$stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Verify password
if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header('Location: ../pages/login.php?tab=login');
    exit();
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];  

// Redirect 
$_SESSION['login_success'] = "Login successful! Welcome back, " . $user['name'] . "!";
header('Location: ../pages/index.php');
exit();
?>