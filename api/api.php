<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized. Please log in.'
    ]);
    exit();
}

// Load API files
require_once "../api/jsearch.php";
require_once "../api/adzuna.php";

require_once "../includes/connection.php";

// Collect and sanitize inputs
$keyword = trim($_GET['keyword'] ?? '');
$location = trim($_GET['location'] ?? 'Philippines');

if ($keyword === '') {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Keyword is required'
    ]);
    exit();
}

// Call each API
$jsearch = fetch_jsearch($keyword, $location);
$adzuna = fetch_adzuna($keyword, $location);

$all = array_merge($jsearch, $adzuna);

try {
    $stmt = $conn->prepare(
        'INSERT INTO search_history (user_id, keyword, location) VALUES (?, ?, ?)'
    );
   $stmt->execute([$_SESSION['user_id'], $keyword, $location]); 
} catch (PDOException $e) {
    error_log('Could not save search:' . $e->getMessage()); 
}

// Return combined results
header ('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'keyword' => $keyword,
    'location' => $location,
    'total' => count($all),
    'jobs' => $all
], JSON_PRETTY_PRINT);
?>