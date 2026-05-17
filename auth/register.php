<?php
session_start();
require_once "../includes/connection.php";

// Only accepts POST method
if ($_SERVER["REQUEST_METHOD"] !== "POST"){
    header('Location: ../pages/login.php');
    exit();
}

// Get the form data
$name = trim($_POST["name"] ?? ""); 
$email = trim($_POST["email"] ?? "");
$password = ($_POST["password"] ?? "");
$confirm = ($_POST["confirm"] ?? "");

// Validation
$error = [];

if (empty($name)) {
    $error[] = "Name is required";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error[] = "Valid email is required";
}

if (strlen($password) < 8) {
    $error[] = "Password must be at least 8 characters";
}

if ($password !== $confirm) {
    $error[] = "Passwords do not match";
}

if (!empty($error)) {
    $_SESSION['register_error'] = $error;
    $_SESSION['register_old'] =  [
        'name' => $name,
        'email' => $email
    ];
    header('Location: ../pages/login.php?tab=register');
    exit();
}

// check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    $_SESSION['register_error'] = ["Email already exists"];
    $_SESSION['register_old'] = [
        'name' => $name,
        'email' => $email
    ];
    header('Location: ../pages/login.php?tab=register');
    exit();
}

// Insert new user
$hashed = password_hash($password, PASSWORD_BCRYPT);

$insert = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?,  ?, ?)");
$insert->execute([
    $name, 
    $email, 
    $hashed
]);

// If Success
$_SESSION['register_success'] = "Account created successfully!";
header('Location: ../pages/login.php?tab=login');
exit();
?>