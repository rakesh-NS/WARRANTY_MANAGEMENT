
<?php
session_start();
include '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get total registered products
$products_sql = "SELECT COUNT(*) as product_count FROM products";
$products_result = mysqli_query($conn, $products_sql);
$products_data = mysqli_fetch_assoc($products_result);
$product_count = $products_data['product_count'];

// Get total warranty claims
$claims_sql = "SELECT COUNT(*) as claim_count FROM warranty_claims";
$claims_result = mysqli_query($conn, $claims_sql);
$claims_data = mysqli_fetch_assoc($claims_result);
$claim_count = $claims_data['claim_count'];

// Get total users
$users_sql = "SELECT COUNT(*) as user_count FROM users WHERE user_type='customer'";
$users_result = mysqli_query($conn, $users_sql);
$users_data = mysqli_fetch_assoc($users_result);
$user_count = $users_data['user_count'];

// Get pending claims
$pending_sql = "SELECT COUNT(*) as pending_count FROM warranty_claims WHERE status='pending'";
$pending_result = mysqli_query($conn, $pending_sql);
$pending_data = mysqli_fetch_assoc($pending_result);
$pending_count = $pending_data['pending_count'];

// Get recent claims
$recent_claims_sql = "SELECT c.id, c.date_submitted, c.status, p.name as product_name, u.name as customer_name
                      FROM warranty_claims c
                      JOIN products p ON c.product_id = p.id
                      JOIN users u ON c.user_id = u.id
                      ORDER BY c.date_submitted DESC
                      LIMIT 5";
$recent_claims_result = mysqli_query($conn, $recent_claims_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Admin Portal</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="categories.php">Product Categories</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <div class="cards-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Registered Products</h3>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $product_count; ?></h2>
                    </div>
                    <a href="products.php" class="btn primary-btn">View All</a>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Total Customers</h3>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $user_count; ?></h2>
                    </div>
                    <a href="customers.php" class="btn primary-btn">View All</a>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Warranty Claims</h3>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $claim_count; ?></h2>
                    </div>
                    <a href="claims.php" class="btn primary-btn">View All</a>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Pending Claims</h3>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $pending_count; ?></h2>
                    </div>
                    <a href="claims.php?status=pending" class="btn primary-btn">View Pending</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Recent Warranty Claims</h3>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($recent_claims_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            <?php while ($claim = mysqli_fetch_assoc($recent_claims_result)): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($claim['date_submitted'])); ?></td>
                                <td><?php echo $claim['product_name']; ?></td>
                                <td><?php echo $claim['customer_name']; ?></td>
                                <td><?php echo ucfirst($claim['status']); ?></td>
                                <td>
                                    <a href="view_claim.php?id=<?php echo $claim['id']; ?>" class="btn secondary-btn">View</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No recent warranty claims found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
