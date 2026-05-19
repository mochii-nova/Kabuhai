<?php
session_start();

// redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/index.php');  
    exit;
}

// active tab
$active_tab = 'login';
if (
    (isset($_GET['tab']) && $_GET['tab'] === 'register') || !empty($_SESSION['register_error'])  
) {
    $active_tab = 'register';
}

// messages
$login_error = $_SESSION['login_error'] ?? null;
$login_success = $_SESSION['login_success'] ?? null;
$register_error = $_SESSION['register_error'] ?? [];
$register_success = $_SESSION['register_success'] ?? null;
$register_old = $_SESSION['register_old'] ?? [];

// Clear messages 
unset(
    $_SESSION['login_error'],
    $_SESSION['login_success'],
    $_SESSION['register_error'],
    $_SESSION['register_success'],
    $_SESSION['register_old']
);

// already logged in
$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabuhai - Login</title>
    <link rel="stylesheet" href="../assets/login.css">
</head>
<body>
    <div class="page-wrapper">
        <h1>Kabuhai</h1>

        <!-- Login Tab -->
        <div class="tab-panel <?= $active_tab === 'login' ? 'active' : '' ?>" id="panel-login">
            <h2>Log in</h2>
            <hr class="divider">

            <!-- Show message -->
            <?php if ($register_success): ?>
                <div class="msg-success"><?= htmlspecialchars($register_success) ?></div>
            <?php endif; ?>

            <?php if ($login_success): ?>
                <div class="msg-success"><?= htmlspecialchars($login_success) ?></div>  
            <?php endif; ?>

            <?php if ($login_error): ?>
                <div class="msg-error"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            
            <!-- POST method (login) -->
            <form action="../auth/login.php" method="POST">
                <div class="form-group">
                    <label for="login-email">Email address</label>
                    <input 
                    type="text"
                    id="login-email"
                    name="email"
                    required
                    autocomplete="email"
                    placeholder="Enter your email address">
                </div>

                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input 
                    type="password"
                    id="login-password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password">
                </div>

                <button type="submit">Log in</button>
            </form>

            <p class="switch-link">
                Don't have an account? 
                <a href="?tab=register">Register here</a>
            </p>
        </div>
    
        <!-- Register Tab -->
        <div class="tab-panel <?= $active_tab === 'register' ? 'active' : '' ?>" id="panel-register">
            <h2>Create an account</h2>
            <hr class="divider">

            <!-- Show error message -->
            <?php if (!empty($register_error)): ?>
                <div class="msg-error">
                    <strong>Please fix the following:</strong>
                    <ul class="error-list">
                        <?php foreach ($register_error as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?> 
                    </ul>
                </div>
            <?php endif; ?>

            <!-- POST method (register) -->
            <form action="../auth/register.php" method="POST">

                <div class="form-group">
                    <label for="reg-name">Full name</label>
                    <input 
                        type="text"
                        id="reg-name"
                        name="name"
                        required
                        autocomplete="name"
                        placeholder="Enter your full name"
                        value="<?= htmlspecialchars($register_old['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="reg-email">Email address</label>
                    <input 
                        type="email"
                        id="reg-email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="Enter your email address"
                        value="<?= htmlspecialchars($register_old['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="reg-password">
                        Password <small>(minimum of 8 characters)</small>
                    </label>
                    <input 
                        type="password"
                        id="reg-password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Create a password">
                </div>

                <div class="form-group">
                    <label for="reg-confirm">Confirm password</label>
                    <input 
                        type="password"
                        id="reg-confirm"
                        name="confirm"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your password">      
                </div>

                <button type="submit">Create account</button>
            </form>

            <p class="switch-link">
                Already have an account?
                <a href="?tab=login">Log in here</a>
            </p>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.getElementById('panel-login').classList.remove('active');
            document.getElementById('panel-register').classList.remove('active');
            document.getElementById('panel-' + tab).classList.add('active');
        }
    </script>
</body>
</html>