<?php
session_start();

// auth
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// collect input
$keyword = trim($_GET['keyword'] ?? '');
$location = trim($_GET['location'] ?? 'Philippines');

if ($keyword === '') {
    header('Location: ../pages/index.php');
    exit();
}

// call api - FIXED PATHS
require_once __DIR__ . '/../api/jsearch.php';     
require_once __DIR__ . '/../api/adzuna.php';      
require_once __DIR__ . '/../includes/connection.php'; 

$jsearch = fetch_jsearch($keyword, $location);
$adzuna = fetch_adzuna($keyword, $location);
$all = array_merge($jsearch, $adzuna);

// search history 
try {
    $stmt = $conn->prepare(
        'INSERT INTO search_history (user_id, keyword, location) VALUES (?, ?, ?)'
    );
    $stmt->execute([$_SESSION['user_id'], $keyword, $location]);
} catch (PDOException $e) {
    error_log('Could not save search history: ' . $e->getMessage());
}

// pass results
$_SESSION['search_results'] = $all;
$_SESSION['search_keyword'] = $keyword;
$_SESSION['search_location'] = $location;

header('Location: ../pages/results.php');
exit();
?>