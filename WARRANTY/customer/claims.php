
<?php
session_start();
include '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all warranty claims for this user
$claims_sql = "SELECT c.id, c.date_submitted, c.issue_description, c.status, 
              p.name as product_name, p.serial_number
              FROM warranty_claims c
              JOIN products p ON c.product_id = p.id
              WHERE c.user_id = $user_id
              ORDER BY c.date_submitted DESC";
$claims_result = mysqli_query($conn, $claims_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Warranty Claims - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Warranty Guardian</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">My Products</a></li>
                <li><a href="register_product.php">Register Product</a></li>
                <li><a href="warranties.php">My Warranties</a></li>
                <li><a href="claims.php" class="active">Warranty Claims</a></li>
                <li><a href="new_claim.php">Submit Claim</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>My Warranty Claims</h1>
                <div>
                    <a href="new_claim.php" class="btn primary-btn">Submit New Claim</a>
                    <a href="../logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <?php if (mysqli_num_rows($claims_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>Claim ID</th>
                                <th>Date Submitted</th>
                                <th>Product</th>
                                <th>Serial Number</th>
                                <th>Issue</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            <?php while ($claim = mysqli_fetch_assoc($claims_result)): ?>
                            <tr>
                                <td><?php echo $claim['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($claim['date_submitted'])); ?></td>
                                <td><?php echo $claim['product_name']; ?></td>
                                <td><?php echo $claim['serial_number']; ?></td>
                                <td><?php echo substr($claim['issue_description'], 0, 50) . (strlen($claim['issue_description']) > 50 ? '...' : ''); ?></td>
                                <td><?php echo ucfirst($claim['status']); ?></td>
                                <td>
                                    <a href="view_claim.php?id=<?php echo $claim['id']; ?>" class="btn secondary-btn">View Details</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>You haven't submitted any warranty claims yet.</p>
                            <a href="new_claim.php" class="btn primary-btn">Submit New Claim</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .empty-state {
            text-align: center;
            padding: 30px;
        }
        
        .empty-state p {
            margin-bottom: 20px;
            font-size: 18px;
            color: #666;
        }
    </style>
</body>
</html>
