
<?php
session_start();
include '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Base SQL query
$claims_sql = "SELECT c.id, c.date_submitted, c.issue_description, c.status, 
              p.name as product_name, p.serial_number, 
              u.name as customer_name, u.email as customer_email
              FROM warranty_claims c
              JOIN products p ON c.product_id = p.id
              JOIN users u ON c.user_id = u.id";

// Apply filter if specified
if (!empty($status_filter)) {
    $claims_sql .= " WHERE c.status = '$status_filter'";
}

$claims_sql .= " ORDER BY c.date_submitted DESC";
$claims_result = mysqli_query($conn, $claims_sql);

// Process claim update if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['claim_id']) && isset($_POST['status'])) {
    $claim_id = $_POST['claim_id'];
    $new_status = $_POST['status'];
    $admin_notes = $_POST['admin_notes'];
    
    $update_sql = "UPDATE warranty_claims SET status = '$new_status', admin_notes = '$admin_notes', 
                  processed_date = CURDATE() WHERE id = $claim_id";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: claims.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warranty Claims - Admin - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Admin Portal</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="claims.php" class="active">Warranty Claims</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="categories.php">Product Categories</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Warranty Claims Management</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <div class="filter-options">
                <a href="claims.php" class="btn <?php echo empty($status_filter) ? 'primary-btn' : 'secondary-btn'; ?>">All Claims</a>
                <a href="claims.php?status=pending" class="btn <?php echo $status_filter == 'pending' ? 'primary-btn' : 'secondary-btn'; ?>">Pending</a>
                <a href="claims.php?status=approved" class="btn <?php echo $status_filter == 'approved' ? 'primary-btn' : 'secondary-btn'; ?>">Approved</a>
                <a href="claims.php?status=rejected" class="btn <?php echo $status_filter == 'rejected' ? 'primary-btn' : 'secondary-btn'; ?>">Rejected</a>
                <a href="claims.php?status=completed" class="btn <?php echo $status_filter == 'completed' ? 'primary-btn' : 'secondary-btn'; ?>">Completed</a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <?php if (mysqli_num_rows($claims_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>ID</th>
                                <th>Date Submitted</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Serial Number</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            <?php while ($claim = mysqli_fetch_assoc($claims_result)): ?>
                            <tr>
                                <td><?php echo $claim['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($claim['date_submitted'])); ?></td>
                                <td><?php echo $claim['customer_name']; ?><br><?php echo $claim['customer_email']; ?></td>
                                <td><?php echo $claim['product_name']; ?></td>
                                <td><?php echo $claim['serial_number']; ?></td>
                                <td><?php echo ucfirst($claim['status']); ?></td>
                                <td>
                                    <a href="view_claim.php?id=<?php echo $claim['id']; ?>" class="btn primary-btn">View Details</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No warranty claims found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
