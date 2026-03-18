<?php
session_start();
require 'db_connect.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch latest user data from DB to ensure it's up to date
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — UC CCS SIT Monitoring</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<nav>
    <a href="dashboard.php" class="nav-brand">
        <img src="UClogo.png" alt="UC Logo">
        <span class="nav-title">College of Computer Studies<br>SIT-IN Monitoring System</span>
    </a>
    <ul class="nav-links">
        <li><a href="dashboard.php" class="active">Home</a></li>
        <li><a href="edit_profile.php">Edit Profile</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="reservation.php">Reservation</a></li>
        <li><a href="logout.php" class="btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="dashboard-container">
    
    <aside class="info-panel">
        <div class="panel-header">Student Information</div>
        <div class="profile-card">
            <div class="avatar-frame">
                <img src="Studentlogo.png" alt="User Avatar">
            </div>
            <div class="info-list">
                <div class="info-item">
                    <span class="label">ID Number:</span>
                    <span class="val"><?= htmlspecialchars($user['id_number']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Name:</span>
                    <span class="val"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Course:</span>
                    <span class="val"><?= htmlspecialchars($user['course']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Year:</span>
                    <span class="val"><?= htmlspecialchars($user['course_level']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Email:</span>
                    <span class="val"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Session:</span>
                    <span class="val session-count">30</span>
                </div>
            </div>
        </div>
    </aside>

    <main class="announcement-panel">
        <div class="panel-header">Announcements</div>
        <div class="scroll-box">
            <div class="post">
                <div class="post-meta">CCS Admin | 2026-Feb-11</div>
                <p>Welcome to the new Semester! Please ensure your SIT-IN hours are logged correctly.</p>
            </div>
            <div class="post">
                <div class="post-meta">CCS Admin | 2024-May-08</div>
                <p>Important Announcement: Explore our latest products and services now! 🎉</p>
            </div>
        </div>
    </main>

    <section class="rules-panel">
        <div class="panel-header">Rules and Regulation</div>
        <div class="scroll-box rules-content">
            <h3>University of Cebu</h3>
            <h4>COLLEGE OF INFORMATION & COMPUTER STUDIES</h4>
            <p><strong>LABORATORY RULES AND REGULATIONS</strong></p>
            <ol>
                <li>Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones must be switched off.</li>
                <li>Games are not allowed inside the lab. This includes computer-related games and card games.</li>
                <li>Surfing the Internet is allowed only with the permission of the instructor.</li>
                <li>Downloading and installing software are strictly prohibited.</li>
            </ol>
        </div>
    </section>

</div>

<footer>
    &copy; <?= date('Y') ?> University of Cebu — CCS SIT Monitoring System
</footer>

</body>
</html>