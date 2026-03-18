<?php
session_start();
require 'db_connect.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// 1. Fetch current user data to populate the form
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim inputs (skip ID Number)
    $last_name    = trim($_POST['last_name'] ?? '');
    $first_name   = trim($_POST['first_name'] ?? '');
    $middle_name  = trim($_POST['middle_name'] ?? '');
    $course       = trim($_POST['course'] ?? '');
    $course_level = trim($_POST['course_level'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $address      = trim($_POST['address'] ?? '');

    // Basic Validation
    if (empty($last_name) || empty($first_name) || empty($email)) {
        $error = 'Please fill in all required fields (marked with *).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            // Update the database
            $sql = "UPDATE users SET 
                    last_name = ?, first_name = ?, middle_name = ?, 
                    course = ?, course_level = ?, email = ?, address = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$last_name, $first_name, $middle_name, $course, $course_level, $email, $address, $_SESSION['user_id']]);

            // Refresh the user variable to show updated data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            // Also update first name in session for the navbar
            $_SESSION['first_name'] = $user['first_name'];

            $success = 'Profile updated successfully!';
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile — UC CCS SIT Monitoring</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css"> <link rel="stylesheet" href="edit_profile.css"> </head>
<body>

<nav>
    <a href="dashboard.php" class="nav-brand">
        <img src="UClogo.png" alt="UC Logo">
        <span class="nav-title">College of Computer Studies<br>SIT-IN Monitoring System</span>
    </a>
    <ul class="nav-links">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="edit_profile.php" class="active">Edit Profile</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="reservation.php">Reservation</a></li>
        <li><a href="logout.php" class="btn-nav">Log out</a></li>
    </ul>
</nav>

<div class="dashboard-container edit-page-container">
    
    <aside class="info-panel sticky-panel">
        <div class="panel-header">Profile Summary</div>
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
            </div>
        </div>
    </aside>

    <main class="form-panel main-form-panel">
        <div class="panel-header">Update Profile Details</div>
        
        <div class="form-content scroll-box">

            <?php if ($error): ?>
                <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success">✓ <?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_profile.php" class="presentable-form">

                <div class="field field-locked">
                    <label for="id_number">ID Number (Read-Only)</label>
                    <div class="input-wrap">
                        <input type="text" id="id_number" value="<?= htmlspecialchars($user['id_number']) ?>" disabled>
                        <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" value="<?= htmlspecialchars($user['middle_name']) ?>">
                    </div>
                </div>

                <div class="form-grid split-3">
                    <div class="field">
  <label for="course">Course *</label>
  <div class="input-wrap">
    <select id="course" name="course" required>
      <option value="" disabled <?= empty($user['course']) ? 'selected' : '' ?>>Select course</option>

      <optgroup label="College of Computer Studies (CCS)">
        <option value="BSIT" <?= ($user['course'] ?? '') === 'BSIT' ? 'selected' : '' ?>>Information Technology</option>
        <option value="BSCS" <?= ($user['course'] ?? '') === 'BSCS' ? 'selected' : '' ?>>Computer Science</option>
        <option value="BSIS" <?= ($user['course'] ?? '') === 'BSIS' ? 'selected' : '' ?>>Information Systems</option>
        <option value="ACT"  <?= ($user['course'] ?? '') === 'ACT'  ? 'selected' : '' ?>>ACT</option>
      </optgroup>

      <optgroup label="College of Engineering">
        <option value="BSCpE" <?= ($user['course'] ?? '') === 'BSCpE' ? 'selected' : '' ?>>Computer Engineering</option>
        <option value="BSCE"  <?= ($user['course'] ?? '') === 'BSCE'  ? 'selected' : '' ?>>Civil Engineering</option>
        <option value="BSME"  <?= ($user['course'] ?? '') === 'BSME'  ? 'selected' : '' ?>>Mechanical Engineering</option>
        <option value="BSEE"  <?= ($user['course'] ?? '') === 'BSEE'  ? 'selected' : '' ?>>Electrical Engineering</option>
        <option value="BSIE"  <?= ($user['course'] ?? '') === 'BSIE'  ? 'selected' : '' ?>>Industrial Engineering</option>
        <option value="BSNAME" <?= ($user['course'] ?? '') === 'BSNAME' ? 'selected' : '' ?>>Naval Architecture and Marine Engineering</option>
      </optgroup>

      <optgroup label="College of Education">
        <option value="BEEd" <?= ($user['course'] ?? '') === 'BEEd' ? 'selected' : '' ?>>Elementary Education (BEEd)</option>
        <option value="BSEd" <?= ($user['course'] ?? '') === 'BSEd' ? 'selected' : '' ?>>Secondary Education (BSEd)</option>
      </optgroup>

      <optgroup label="Criminal Justice & Arts">
        <option value="BS Crim" <?= ($user['course'] ?? '') === 'BS Crim' ? 'selected' : '' ?>>Criminology</option>
        <option value="IndPsych" <?= ($user['course'] ?? '') === 'IndPsych' ? 'selected' : '' ?>>Industrial Psychology</option>
        <option value="AB PolSci" <?= ($user['course'] ?? '') === 'AB PolSci' ? 'selected' : '' ?>>AB Political Science</option>
        <option value="AB English" <?= ($user['course'] ?? '') === 'AB English' ? 'selected' : '' ?>>AB English</option>
      </optgroup>

      <optgroup label="Business & Management">
        <option value="BS Commerce" <?= ($user['course'] ?? '') === 'BS Commerce' ? 'selected' : '' ?>>Commerce</option>
        <option value="BS Accountancy" <?= ($user['course'] ?? '') === 'BS Accountancy' ? 'selected' : '' ?>>Accountancy</option>
        <option value="BSHRM" <?= ($user['course'] ?? '') === 'BSHRM' ? 'selected' : '' ?>>Hotel and Restaurant Management</option>
        <option value="BSCA" <?= ($user['course'] ?? '') === 'BSCA' ? 'selected' : '' ?>>Customs Administration</option>
        <option value="CompSec" <?= ($user['course'] ?? '') === 'CompSec' ? 'selected' : '' ?>>Computer Secretarial</option>
      </optgroup>

      <optgroup label="Special Programs & Short Courses">
        <option value="CISCO" <?= ($user['course'] ?? '') === 'CISCO' ? 'selected' : '' ?>>CISCO Networking Academy (Module 1 - 4)</option>
        <option value="EngComm" <?= ($user['course'] ?? '') === 'EngComm' ? 'selected' : '' ?>>English Communication Skills</option>
        <option value="Korean" <?= ($user['course'] ?? '') === 'Korean' ? 'selected' : '' ?>>Conversational Korean</option>
      </optgroup>
    </select>
  </div>
</div>
                    <div class="field">
                        <label for="course_level">Year Level</label>
                        <input type="number" id="course_level" name="course_level" value="<?= htmlspecialchars($user['course_level']) ?>" min="1" max="5" required>
                    </div>
                </div>

                <div class="field">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="field">
                    <label for="address">Address *</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
                </div>

                <button type="submit" class="btn-primary">Save Changes</button>
            </form>
        </div>
    </main>
</div>

<footer>
    &copy; <?= date('Y') ?> University of Cebu — CCS SIT Monitoring System
</footer>

</body>
</html>