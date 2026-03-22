<?php
require 'config.php';
// require 'AuthenticationSec/adminlogincheck.php';

$success = '';
$error = '';

// ===============================
// HANDLE ADD CUSTOMER
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {

    $full_name   = trim($_POST['full_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $institution = trim($_POST['institution_name'] ?? '');
    $password    = $_POST['password'] ?? '';

    // Validation
    if ($full_name === '' || $email === '' || $password === '') {
        $error = 'Name, email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT ID FROM users WHERE Email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'A user with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'customer';

            $stmt = $conn->prepare("INSERT INTO users (Full_Name, Email, institution_Name, Password_Hash, Role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $full_name, $email, $institution, $hash, $role);

            if ($stmt->execute()) {
                $success = 'Customer added successfully.';
            } else {
                $error = 'Failed to add customer. Please try again.';
            }
            $stmt->close();
        }
        $check->close();
    }
}

// ===============================
// HANDLE UPDATE CUSTOMER
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_customer'])) {

    $user_id     = intval($_POST['user_id'] ?? 0);
    $full_name   = trim($_POST['full_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $institution = trim($_POST['institution_name'] ?? '');

    if ($user_id === 0 || $full_name === '' || $email === '') {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check email not taken by another user
        $check = $conn->prepare("SELECT ID FROM users WHERE Email = ? AND ID != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'That email is already used by another account.';
        } else {
            $stmt = $conn->prepare("UPDATE users SET Full_Name = ?, Email = ?, institution_Name = ? WHERE ID = ?");
            $stmt->bind_param("sssi", $full_name, $email, $institution, $user_id);

            if ($stmt->execute()) {
                $success = 'Customer updated successfully.';
            } else {
                $error = 'Failed to update customer.';
            }
            $stmt->close();
        }
        $check->close();
    }
}

// ===============================
// SEARCH FILTER
// ===============================
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT ID, Full_Name, Email, institution_Name, Role
        FROM users
        WHERE Role = 'customer'
        AND (Full_Name LIKE ? OR Email LIKE ? OR institution_Name LIKE ?)
        ORDER BY ID DESC
    ");
    $like = "%" . $search . "%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $customers = $stmt->get_result();
} else {
    $customers = $conn->query("
        SELECT ID, Full_Name, Email, institution_Name, Role
        FROM users
        WHERE Role = 'customer'
        ORDER BY ID DESC
    ");
}

// Count total customers
$count_result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE Role = 'customer'");
$total_customers = $count_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customers | NovaTech Admin</title>
    <link rel="stylesheet" href="Styles/admin.css">
    <style>
        /* Modal overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 10px;
            padding: 30px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-box h2 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        .modal-box label {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
            margin-top: 14px;
        }

        .modal-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .modal-box input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .modal-actions {
            margin-top: 24px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Alert messages */
        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Toggle for add form */
        .add-form {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .add-form.visible {
            display: block;
        }

        .add-form h2 {
            margin-bottom: 15px;
        }

        .add-form .form-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .add-form .form-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 150px;
        }

        .add-form .form-group label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .add-form .form-group input {
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        /* Action buttons in table cell */
        .action-cell {
            display: flex;
            gap: 6px;
            align-items: center;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>NovaTech Admin</h2>
    <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_products.php">products</a></li>
        <li><a href="admin_inventory.php">Inventory</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="admin_customers.php">Customers</a></li>
        <li><a href="Admin_reviews.php">Reviews</a></li>
        <li><a href="Admin_returns.php">Returns</a></li>
        <li><a href="switch_to_customer.php">View as Customer</a></li>
        <li><a href="AuthenticationSec/adminchangepw.php">Change Password</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="header">
        <h1>Customers</h1>
    </div>

    <!-- SUCCESS / ERROR MESSAGES -->
    <?php if ($success !== ''): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- STAT CARD -->
    <div class="analytics-grid" style="margin-bottom:20px;">
        <div class="card">
            <h3>Total Customers</h3>
            <p><?= $total_customers ?></p>
        </div>
    </div>

    <!-- SEARCH + ADD BUTTON ROW -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <input type="text" name="search" placeholder="Search by name, email or institution..."
                   value="<?= htmlspecialchars($search) ?>" style="width:280px;">
            <button type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a href="admin_customers.php" style="padding:6px 12px; background:#6b7280; color:white; border-radius:5px; text-decoration:none; font-size:14px;">Clear</a>
            <?php endif; ?>
        </form>

        <button onclick="toggleAddForm()" style="background:#10b981;">+ Add Customer</button>
    </div>

    <!-- ADD CUSTOMER FORM (hidden by default) -->
    <div class="add-form" id="addForm">
        <h2>Add New Customer</h2>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" placeholder="John Smith" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label>Institution</label>
                    <input type="text" name="institution_name" placeholder="e.g. Aston University">
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" placeholder="Min 8 characters" required>
                </div>
            </div>
            <div style="margin-top:14px;">
                <button type="submit" name="add_customer">Add Customer</button>
                <button type="button" class="secondary" onclick="toggleAddForm()">Cancel</button>
            </div>
        </form>
    </div>

    <!-- CUSTOMERS TABLE -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Institution</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php if ($customers->num_rows === 0): ?>
        <tr>
            <td colspan="6" style="text-align:center; color:#6b7280; padding:20px;">
                <?= $search !== '' ? 'No customers found matching "' . htmlspecialchars($search) . '".' : 'No customers yet.' ?>
            </td>
        </tr>
        <?php endif; ?>

        <?php while($user = $customers->fetch_assoc()): ?>
        <tr>
            <td><?= $user['ID']; ?></td>
            <td><?= htmlspecialchars($user['Full_Name']); ?></td>
            <td><?= htmlspecialchars($user['Email']); ?></td>
            <td><?= htmlspecialchars($user['institution_Name'] ?? ''); ?></td>
            <td><?= $user['Role']; ?></td>
            <td>
                <div class="action-cell">
                    <!-- EDIT BUTTON -->
                    <button onclick="openEditModal(
                        <?= $user['ID'] ?>,
                        '<?= htmlspecialchars(addslashes($user['Full_Name']), ENT_QUOTES) ?>',
                        '<?= htmlspecialchars(addslashes($user['Email']), ENT_QUOTES) ?>',
                        '<?= htmlspecialchars(addslashes($user['institution_Name'] ?? ''), ENT_QUOTES) ?>'
                    )">Edit</button>

                    <!-- DELETE BUTTON -->
                    <form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this customer?');" style="margin:0;">
                        <input type="hidden" name="user_id" value="<?= $user['ID']; ?>">
                        <button class="danger">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

<!-- EDIT CUSTOMER MODAL -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <h2>Edit Customer</h2>
        <form method="POST">
            <input type="hidden" name="user_id" id="edit-id">

            <label>Full Name</label>
            <input type="text" name="full_name" id="edit-name" required>

            <label>Email Address</label>
            <input type="email" name="email" id="edit-email" required>

            <label>Institution / Organisation</label>
            <input type="text" name="institution_name" id="edit-institution" placeholder="e.g. Aston University">

            <div class="modal-actions">
                <button type="button" class="secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" name="update_customer">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle add customer form
function toggleAddForm() {
    document.getElementById('addForm').classList.toggle('visible');
}

// Open edit modal and populate the fields
function openEditModal(id, name, email, institution) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-institution').value = institution;
    document.getElementById('editModal').classList.add('active');
}

// Close edit modal
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Close modal when clicking outside the box
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

</body>
</html>