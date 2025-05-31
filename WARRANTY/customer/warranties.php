
<?php
session_start();
include '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all warranties for this user's products
$warranties_sql = "SELECT w.*, p.name as product_name, p.serial_number, c.name as category_name
                  FROM warranties w
                  JOIN products p ON w.product_id = p.id
                  JOIN product_categories c ON p.category_id = c.id
                  WHERE p.user_id = $user_id
                  ORDER BY w.expiry_date ASC";
$warranties_result = mysqli_query($conn, $warranties_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Warranties - Warranty Guardian System</title>
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
                <li><a href="warranties.php" class="active">My Warranties</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="new_claim.php">Submit Claim</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>My Warranties</h1>
                <div>
                    <a href="register_product.php" class="btn primary-btn">Register New Product</a>
                    <a href="../logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <?php if (mysqli_num_rows($warranties_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Serial Number</th>
                                <th>Start Date</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Days Remaining</th>
                                <th>Action</th>
                            </tr>
                            <?php while ($warranty = mysqli_fetch_assoc($warranties_result)): 
                                $today = time();
                                $expiry = strtotime($warranty['expiry_date']);
                                $days_remaining = round(($expiry - $today) / (60 * 60 * 24));
                                
                                $status = "Expired";
                                $status_class = "text-danger";
                                
                                if ($days_remaining > 0) {
                                    $status = "Active";
                                    $status_class = "text-success";
                                }
                            ?>
                            <tr>
                                <td><?php echo $warranty['product_name']; ?></td>
                                <td><?php echo $warranty['category_name']; ?></td>
                                <td><?php echo $warranty['serial_number']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($warranty['start_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($warranty['expiry_date'])); ?></td>
                                <td class="<?php echo $status_class; ?>"><?php echo $status; ?></td>
                                <td>
                                    <?php 
                                    if ($days_remaining > 0) {
                                        echo $days_remaining . " days";
                                    } else {
                                        echo "Expired";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($status == "Active"): ?>
                                        <a href="new_claim.php?product_id=<?php echo $warranty['product_id']; ?>" class="btn secondary-btn">Submit Claim</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>You don't have any products with registered warranties yet.</p>
                            <a href="register_product.php" class="btn primary-btn">Register a Product with Warranty</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .text-danger { color: #e74c3c; }
        .text-success { color: #2ecc71; }
        .text-warning { color: #f39c12; }
        
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
