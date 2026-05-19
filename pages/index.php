<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../pages/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabuhai - Home</title>
    <link rel="stylesheet" href="../assets/index.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <span><strong>Kabuhai</strong></span>
        <div>
            <span>Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="../pages/dashboard.php">Dashboard</a>
            <a href="?logout=1">Log out</a>  
        </div>
    </nav>

    <!-- Home content -->
     <div class="home-wrapper">
        <h1>Find your next job</h1>
        <p class="tagline">Search thousands of job listings across the Philippines.</p>

        <form class="search-form" action="../pages/search.php" method="GET">
            <div class="search-row">
                <input 
                type="text"
                name="keyword"
                placeholder="Job title, skill, or keyword"
                required>
                <input 
                type="text"
                name="location"
                placeholder="City (e.g. Dagupan, Mangaldan, San Fabian)"
                required>
            </div>

            <button type="submit">Search jobs</button>
        </form>
     </div>
</body>
</html>