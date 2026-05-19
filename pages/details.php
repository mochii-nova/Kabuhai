<?php
session_start();

// Auth
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// Get job
$index = (int) ($_GET['index'] ?? -1);
$jobs = $_SESSION['search_results'] ?? [];

if ($index < 0 || !isset($jobs[$index])) {
    header('Location: ../pages/results.php');
    exit();
}

$jobs = $jobs[$index];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabuhai - <?= htmlspecialchars($jobs['title']) ?></title>
    <link rel="stylesheet" href="../assets/details.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <a href="../pages/index.php"><strong>Kabuhai</strong></a>
        <div>
            <span>Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="/kabuhai/pages/dashboard.php">Dashboard</a>
            <a href="/kabuhai/pages/login.php">Log out</a>
        </div>
    </nav>

    <div class="detail-wrapper">
        <a class="back-link" href="../pages/results.php">&larr; Back to results</a>

        <!-- Header -->
        <div class="job-header">
            <h1><?= trim(htmlspecialchars($jobs['title'])) ?></h1>
            <div class="job-meta">
                <span><strong>Company:</strong> <?= htmlspecialchars($jobs['company']) ?></span>
                <span><strong>Location:</strong> <?= htmlspecialchars($jobs['location']) ?></span>
                <span><strong>Source:</strong> <?= htmlspecialchars($jobs['source']) ?></span>
            </div>
            <?php if (!empty($jobs['posted_at'])): ?>
                <div class="job-meta">
                    <span><strong>Posted:</strong> <?= htmlspecialchars($jobs['posted_at']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Description -->
        <p class="section-label">Job description</p>
        <div class="job-description">
            <?= trim(htmlspecialchars($jobs['description'] ?: 'No description provided'))  ?>
        </div>

        <!-- Actions -->
        <div class="job-actions">
            <!-- Apply -->
            <?php if (!empty($jobs['url'])): ?>
                <a href="<?= htmlspecialchars($jobs['url']) ?>" target="_blank" rel="noopener noreferrer">
                    Apply now
                </a>
            <?php endif; ?>

            <!-- Save -->
            <form action="../saved/save.php" method="POST" style="margin: 0">
                <input type="hidden" name="job_index" value="<?= $index ?>">
                <button type="submit">Save job</button>
            </form>
        </div>
    </div>
</nav>
</body>
</html>