<?php
session_start();
require 'Config.php';

// redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Loginpage.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// ===============================
// HANDLE PROFILE UPDATE
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name   = trim($_POST['full_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $institution = trim($_POST['institution_name'] ?? '');

    if ($full_name === '' || $email === '') {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // check if email is taken by another user
        $check = $conn->prepare("SELECT ID FROM users WHERE Email = ? AND ID != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'That email is already in use by another account.';
        } else {
            $stmt = $conn->prepare("UPDATE users SET Full_Name = ?, Email = ?, institution_Name = ? WHERE ID = ?");
            $stmt->bind_param("sssi", $full_name, $email, $institution, $user_id);
            if ($stmt->execute()) {
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                $success = 'Profile updated successfully.';
            } else {
                $error = 'Failed to update profile. Please try again.';
            }
            $stmt->close();
        }
        $check->close();
    }
}

// ===============================
// HANDLE PASSWORD CHANGE
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pw = $_POST['current_password'] ?? '';
    $new_pw     = $_POST['new_password'] ?? '';
    $confirm_pw = $_POST['confirm_password'] ?? '';

    if ($current_pw === '' || $new_pw === '' || $confirm_pw === '') {
        $error = 'All password fields are required.';
    } elseif (strlen($new_pw) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new_pw !== $confirm_pw) {
        $error = 'New passwords do not match.';
    } else {
        // verify current password
        $stmt = $conn->prepare("SELECT Password_Hash FROM users WHERE ID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_pw = $result->fetch_assoc();
        $stmt->close();

        if (!password_verify($current_pw, $user_pw['Password_Hash'])) {
            $error = 'Current password is incorrect.';
        } else {
            $new_hash = password_hash($new_pw, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET Password_Hash = ? WHERE ID = ?");
            $stmt->bind_param("si", $new_hash, $user_id);
            if ($stmt->execute()) {
                $success = 'Password changed successfully.';
            } else {
                $error = 'Failed to change password.';
            }
            $stmt->close();
        }
    }
}

// ===============================
// HANDLE ACCOUNT DELETION
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    session_destroy();
    header('Location: Loginpage.php?msg=deleted');
    exit();
}

// ===============================
// FETCH CURRENT USER DATA
// ===============================
$stmt = $conn->prepare("SELECT Full_Name, Email, institution_Name, Role FROM users WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// ===============================
// FETCH RETURN COUNT FOR BADGE
// ===============================
$ret_count = 0;
$ret_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM returns WHERE user_id = ?");
$ret_stmt->bind_param("i", $user_id);
$ret_stmt->execute();
$ret_result = $ret_stmt->get_result();
$ret_row = $ret_result->fetch_assoc();
$ret_count = $ret_row['total'] ?? 0;
$ret_stmt->close();

// pending returns
$pending_ret = 0;
$pret_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM returns WHERE user_id = ? AND status IN ('Requested','Pending')");
$pret_stmt->bind_param("i", $user_id);
$pret_stmt->execute();
$pret_result = $pret_stmt->get_result();
$pret_row = $pret_result->fetch_assoc();
$pending_ret = $pret_row['total'] ?? 0;
$pret_stmt->close();

// order count
$order_count = 0;
$ord_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE User_ID = ?");
$ord_stmt->bind_param("i", $user_id);
$ord_stmt->execute();
$ord_result = $ord_stmt->get_result();
$ord_row = $ord_result->fetch_assoc();
$order_count = $ord_row['total'] ?? 0;
$ord_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | NovaTech</title>
    <link rel="stylesheet" href="Styles/Home.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* ---- LAYOUT ---- */
        .profile-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 5%;
        }

        /* ---- QUICK LINKS ---- */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        .quick-card {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            transition: transform 0.15s, box-shadow 0.15s;
            border: 1px solid #f0f0f0;
        }
        .quick-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .quick-card .qc-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .quick-card .qc-icon.blue   { background: #e8f0fe; }
        .quick-card .qc-icon.red    { background: #fde8e8; }
        .quick-card .qc-icon.green  { background: #e6f9ee; }
        .quick-card .qc-info strong { font-size: 15px; display: block; margin-bottom: 2px; }
        .quick-card .qc-info span   { font-size: 13px; color: #888; }
        .quick-card .qc-badge {
            margin-left: auto;
            background: #dc3545;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 999px;
        }

        /* ---- PROFILE SECTIONS ---- */
        .profile-section {
            background: #fff;
            border-radius: 14px;
            padding: 28px 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 14px rgba(0,0,0,0.05);
        }
        .profile-section h2 {
            font-size: 19px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-section h2 .section-icon {
            font-size: 20px;
        }

        /* ---- FORM ---- */
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 14px;
            color: #444;
        }
        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #0d6bcb;
            box-shadow: 0 0 0 3px rgba(13,107,203,0.1);
        }
        .form-group input:read-only {
            background: #f8f9fa;
            color: #888;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* ---- BUTTONS ---- */
        .btn-primary {
            padding: 11px 28px;
            background: #0d6bcb;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #0b5aa7; }
        .btn-danger {
            padding: 11px 28px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
        }
        .btn-danger:hover { background: #b02a37; }

        /* ---- ALERTS ---- */
        .alert {
            padding: 14px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* ---- USER HEADER ---- */
        .user-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        .user-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #0d6bcb;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .user-header-info h1 {
            font-size: 24px;
            margin-bottom: 4px;
        }
        .user-header-info p {
            font-size: 14px;
            color: #888;
        }
        .user-header-info .role-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: #e8f0fe;
            color: #0d6bcb;
            margin-left: 8px;
        }

        /* ---- DELETE SECTION ---- */
        .delete-section {
            background: #fff;
            border-radius: 14px;
            padding: 28px 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 14px rgba(0,0,0,0.05);
            border: 1px solid #fecaca;
        }
        .delete-section h2 {
            font-size: 19px;
            color: #dc3545;
            margin-bottom: 12px;
        }
        .delete-section p {
            color: #666;
            font-size: 14px;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .user-header { flex-direction: column; text-align: center; }
            .quick-links { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
    <?php require_once __DIR__ . '/topbar.php'; ?>
</header>
<div class="header2"><nav class="nav2"></nav></div>

<!-- Hero -->
<section class="hero">
    <img src="Assets/Home/Hero.png" alt="Profile Banner">
    <div class="hero-text">
        <h1>My Account</h1>
        <p>Manage your profile, orders, and returns</p>
    </div>
</section>

<div class="profile-wrapper">

    <!-- Alerts -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- User Header -->
    <div class="user-header">
        <div class="user-avatar">
            <?= strtoupper(substr($user['Full_Name'], 0, 1)) ?>
        </div>
        <div class="user-header-info">
            <h1>
                <?= htmlspecialchars($user['Full_Name']) ?>
                <span class="role-badge"><?= ucfirst($user['Role']) ?></span>
            </h1>
            <p><?= htmlspecialchars($user['Email']) ?></p>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="quick-links">
        <a href="dashboard.php" class="quick-card">
            <div class="qc-icon blue">&#128230;</div>
            <div class="qc-info">
                <strong>My Orders</strong>
                <span><?= $order_count ?> order<?= $order_count !== 1 ? 's' : '' ?></span>
            </div>
        </a>
        <a href="customer_returns.php" class="quick-card">
            <div class="qc-icon red">&#8634;</div>
            <div class="qc-info">
                <strong>My Returns</strong>
                <span><?= $ret_count ?> return<?= $ret_count !== 1 ? 's' : '' ?></span>
            </div>
            <?php if ($pending_ret > 0): ?>
                <span class="qc-badge"><?= $pending_ret ?> pending</span>
            <?php endif; ?>
        </a>
        <a href="productpage.php" class="quick-card">
            <div class="qc-icon green">&#128722;</div>
            <div class="qc-info">
                <strong>Shop Products</strong>
                <span>Browse catalogue</span>
            </div>
        </a>
    </div>

    <!-- Personal Details -->
    <div class="profile-section">
        <h2><span class="section-icon">&#128100;</span> Personal Details</h2>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['Full_Name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Institution / Organisation</label>
                <input type="text" name="institution_name" value="<?= htmlspecialchars($user['institution_Name']) ?>" placeholder="e.g. Aston University">
            </div>
            <div class="form-group">
                <label>Account Type</label>
                <input type="text" value="<?= ucfirst($user['Role']) ?>" readonly>
            </div>
            <button type="submit" name="update_profile" class="btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="profile-section">
        <h2><span class="section-icon">&#128274;</span> Change Password</h2>
        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="Enter your current password" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="Min 8 characters" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Re-enter new password" required>
                </div>
            </div>
            <button type="submit" name="change_password" class="btn-primary">Change Password</button>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="delete-section">
        <h2>&#9888; Delete Account</h2>
        <p>This will permanently remove your account and all associated data including orders, reviews, and returns. This action cannot be undone.</p>
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
            <button type="submit" name="delete_account" class="btn-danger">Delete My Account</button>
        </form>
    </div>

</div>

<!-- Footer -->
<footer class="footer">
    <div class="col">
        <h4>Store Location</h4>
        <p>Aston University<br>Birmingham</p>
        <p>NovaTech@gmail.com<br>07378867181</p>
    </div>
    <div class="col">
        <h4>Shop</h4>
        <a href="productpage.php">Shop All</a>
        <a href="productpage.php">Computers</a>
        <a href="productpage.php">Projectors</a>
        <a href="productpage.php">Smart Boards</a>
        <a href="productpage.php">Classroom Audio</a>
    </div>
    <div class="col">
        <h4>Support</h4>
        <a href="ContactUs.php">Contact Us</a>
        <a href="aboutpage.php">About Us</a>
    </div>
</footer>

<section class="payment-options">
    <h4>We accept the following paying methods:</h4>
    <div class="payment-icons">
        <img src="Assets/Home/visa.svg">
        <img src="Assets/Home/mastercard.svg">
        <img src="Assets/Home/paypal.svg">
        <img src="Assets/Home/amex.svg">
    </div>
</section>

<script src="javascript/cartCount.js"></script>
</body>
</html>