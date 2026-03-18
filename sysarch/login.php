<?php
session_start();
require 'db_connect.php'; // Ensure you have created this file (see below)

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = trim($_POST['id_number'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (empty($id_number) || empty($password)) {
        $error = 'Please enter both ID number and password.';
    } else {
        try {
            // 1. Look for the user by ID number
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id_number = ?");
            $stmt->execute([$id_number]);
            $user = $stmt->fetch();

            // 2. If user exists, verify the password hash
            if ($user && password_verify($password, $user['password'])) {
                // Login successful! Store data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['id_number'] = $user['id_number'];
                $_SESSION['first_name'] = $user['first_name'];
                
                // Redirect to dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                // Generic error for security (don't tell them if it was the ID or the PW that was wrong)
                $error = 'Invalid ID number or password. Please try again.';
            }
        } catch (PDOException $e) {
            $error = "Connection error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — UC CCS SIT Monitoring System</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="login.css"/>
</head>
<body>

<!-- NAVBAR -->
<nav>
  <a href="index.php" class="nav-brand">
    <img src="UClogo.png" alt="UC Logo">
    <span class="nav-title">College of Computer Studies<br>SIT-IN Monitoring System</span>
  </a>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="community.php">Community</a></li>
    <li><a href="login.php" class="active">Login</a></li>
    <li><a href="Register.php" class="btn-nav">Register</a></li>
  </ul>
</nav>

<!-- MAIN LAYOUT -->
<div class="page">

  <!-- LEFT PANEL -->
  <div class="left-panel">
    <div class="hex-grid"></div>
    <div class="shield-wrapper">
      <img src="Css_logo-removebg-preview.png" alt="CCS Shield">
    </div>
    <div class="left-copy">
      <h2>College of Computer Studies</h2>
      <div class="tagline-pills">
        <span class="pill">SIT Program</span>
        <span class="pill">UC Cebu</span>
        <span class="pill">Est. 1983</span>
      </div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <div class="card">

      <div class="card-logo">
        <img src="UClogo.png" alt="UC Logo">
        <h1>Welcome</h1>
        <p>Sign in to your SIT-IN Monitoring account</p>
      </div>

      <div class="divider"></div>

      <?php if ($error): ?>
      <div class="alert alert-error">
        <span class="alert-icon">⚠</span>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="POST" action="login.php" autocomplete="off">

        <div class="field">
          <label for="id_number">Student / Staff ID</label>
          <div class="input-wrap">
            <input
              type="text"
              id="id_number"
              name="id_number"
              placeholder="e.g. 2024-00001"
              value="<?= htmlspecialchars($_POST['id_number'] ?? '') ?>"
              required
              autocomplete="username"
            >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 9a3 3 0 11-6 0 3 3 0 016 0zm6 9a9 9 0 10-18 0"/>
            </svg>
          </div>
        </div>

        <div class="field">
  <label for="password">Password</label>
  <div class="input-wrap">
    <svg class="input-icon-left" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>

    <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Toggle password">
      <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="17" height="17">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
      </svg>
    </button>

    <input
      type="password"
      id="password"
      name="password"
      placeholder="Enter your password"
      required
      autocomplete="current-password"
    >
  </div>
</div>

        <div class="extras">
          <label class="remember">
            <input type="checkbox" name="remember" <?= isset($_POST['remember']) ? 'checked' : '' ?>>
            Remember me
          </label>
          <a href="forgot-password.php" class="forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn-login">Sign In</button>

      </form>

      <div class="register-row">
        Don't have an account? <a href="register.php">Create one &rarr;</a>
      </div>

    </div>
  </div>

  <footer>
    &copy; <?= date('Y') ?> University of Cebu — College of Computer Studies &nbsp;|&nbsp; SIT Monitoring System
  </footer>

</div>

<script ></script>
</body>
</html>