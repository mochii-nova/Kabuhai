<?php
session_start();

// auth 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// logout
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// read result
if (!isset($_SESSION['search_results'])) {
    header('Location: ../pages/index.php');
    exit();
}

$jobs = $_SESSION['search_results'];
$keyword = $_SESSION['search_keyword'] ?? '';
$location = $_SESSION['search_location'] ?? '';
$total = count($jobs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabuhai - Results for "<?= htmlspecialchars($keyword) ?>"</title>
    <link rel="stylesheet" href="../assets/result.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <a href="../pages/index.php"><strong>Kabuhai</strong></a>
        <div>
            <span>Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="../pages/dashboard.php">Dashboard</a>
            <a href="?logout=1">Log out</a>
        </div>
    </nav>

    <!-- Search Again Bar -->
    <div class="results-wrapper">
        <form class="search-again" action="../pages/search.php" method="GET">
            <input 
            type="text"
            name="keyword"
            value="<?= htmlspecialchars($keyword) ?>"
            placeholder="Keyword"
            required>
            <input 
            type="text"
            name="location"
            value="<?= htmlspecialchars($location) ?>"
            placeholder="Location"
            >
            <button type="submit">Search again</button>
        </form>

        <!-- Result count -->
        <p class="result-count">
            <?php if ($total > 0): ?>
                Found <strong><?= $total ?></strong> job 
                <?= $total !== 1 ? 's' : '' ?> for 
                "<strong><?= htmlspecialchars($keyword) ?></strong>" 
                in <strong><?= htmlspecialchars($location) ?></strong>.
            <?php else: ?>
                No jobs found for "<strong><?= htmlspecialchars($keyword) ?></strong>"
                in <strong><?= htmlspecialchars($location) ?></strong>.
            <?php endif; ?>
        </p>

        <!-- Job cards -->
        <?php if ($total > 0): ?>

            <?php foreach ($jobs as $index => $job): ?>
                <div class="job-card">
                    <h2><?= htmlspecialchars($job['title']) ?></h2>

                    <div class="job meta">
                        <span><?= htmlspecialchars($job['company']) ?></span>
                        <span><?= htmlspecialchars($job['location']) ?></span>
                        <span><?= htmlspecialchars($job['source']) ?></span>
                    </div>

                    <!-- Truncated -->
                    <p class="job-description">
                        <?php 
                        $desc = $job['description'] ?? '';
                        $desc = str_replace('•', '•', $desc);
                        $desc = preg_replace('/^-\s+/m', '•', $desc);
                        echo nl2br(htmlspecialchars($desc));
                    ?>
                    </p>

                    <div class="card-actions">
                        <!-- view details -->
                        <a href="../pages/details.php?index=<?= $index ?>">View details</a>

                        <!-- save job form -->
                        <form action="../saved/save.php" method="POST" style="margin:0">
                            <input 
                            type="hidden"
                            name="job_index"
                            value="<?= $index ?>">
                            <button type="submit">Save job</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="no-results">
                <p>Try a different keyword or broaden your location.</p>
                <p style="margin-top: 12px;">
                    <a href="../pages/index.php">Back to home</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>