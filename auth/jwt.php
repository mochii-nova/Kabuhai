<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// JWT helper
// Base64 encoding
function jwt_based64_encode(string $data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_',), '=');
}

//Base64 decoding
function jwt_based64_decode(string $data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

// Create JWT Token
function jwt_generate(array $payload): string {
    require_once '../includes/config.php';

    $header = jwt_based64_encode(json_encode([
        'alg' => 'HS256',
        'typ' => 'JWT'
    ]));

    $signature = jwt_based64_encode(
        hash_hmac('sha256', "$header.$body", JWT_SECRET, true)
    );

    return "$header.$body.$signature";
}

// Verify JWT Token
function jwt_verify(string $token) {
    require_once '../includes/config.php';

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }

    [$header, $body, $signature] = $parts;

    $expected = jwt_based64_encode(
        hash_hmac('sha256', "$header.$body", JWT_SECRET, true)
    );

    if (!hash_equals($expected, $signature)) {
        return false;
    }

    return $payload;
}

// Make JWT available
$jwt_payload = [
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];

$jwt_token = jwt_generate($jwt_payload);
?>