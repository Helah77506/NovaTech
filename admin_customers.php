<?php
require 'config.php';
// require 'AuthenticationSec/adminlogincheck.php';

// FETCH ALL CUSTOMERS
$customers = $conn->query("
    SELECT ID, Full_Name, Email, Role
    FROM users
    WHERE Role = 'customer'
    ORDER BY ID DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customers | NovaTech Admin</title>
    <link rel="stylesheet" href="Styles/admin.css">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>NovaTech Admin</h2>
    <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_products.php">Products</a></li>
        <li><a href="admin_inventory.php">Inventory</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="admin_customers.php" class="active">Customers</a></li>
        <li><a href="#">Reviews</a></li>
        <li><a href="#">Returns</a></li>
        <li class="bottom-link"><a href="adminchangepw.php">Change Password</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="header">
        <h1>Customers</h1>
    </div>

    <!-- TABLE -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php while($user = $customers->fetch_assoc()): ?>
        <tr>
            <td><?= $user['ID']; ?></td>
            <td><?= htmlspecialchars($user['Full_Name']); ?></td>
            <td><?= htmlspecialchars($user['Email']); ?></td>
            <td><?= $user['Role']; ?></td>
            <td>
                <form method="POST" action="delete_user.php" onsubmit="return confirm('Delete this user?');">
                    <input type="hidden" name="user_id" value="<?= $user['ID']; ?>">
                    <button class="danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>