
<?php
session_start();
include '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all products for this user
$products_sql = "SELECT p.*, c.name as category_name, w.start_date, w.expiry_date
                FROM products p
                JOIN product_categories c ON p.category_id = c.id
                LEFT JOIN warranties w ON p.id = w.product_id
                WHERE p.user_id = $user_id
                ORDER BY p.registration_date DESC";
$products_result = mysqli_query($conn, $products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Warranty Guardian</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php" class="active">My Products</a></li>
                <li><a href="register_product.php">Register Product</a></li>
                <li><a href="warranties.php">My Warranties</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="new_claim.php">Submit Claim</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>My Products</h1>
                <div>
                    <a href="register_product.php" class="btn primary-btn">Register New Product</a>
                    <a href="../logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <?php if (mysqli_num_rows($products_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Serial Number</th>
                                <th>Purchase Date</th>
                                <th>Warranty Status</th>
                                <th>Expires</th>
                                <th>Action</th>
                            </tr>
                            <?php while ($product = mysqli_fetch_assoc($products_result)): 
                                $warranty_status = "No Warranty";
                                $status_class = "text-muted";
                                
                                if ($product['expiry_date']) {
                                    if (strtotime($product['expiry_date']) > time()) {
                                        $warranty_status = "Active";
                                        $status_class = "text-success";
                                    } else {
                                        $warranty_status = "Expired";
                                        $status_class = "text-danger";
                                    }
                                }
                            ?>
                            <tr>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category_name']; ?></td>
                                <td><?php echo $product['serial_number']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($product['purchase_date'])); ?></td>
                                <td class="<?php echo $status_class; ?>">
                                    <?php echo $warranty_status; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($product['expiry_date']) {
                                        echo date('M d, Y', strtotime($product['expiry_date']));
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($warranty_status == "Active"): ?>
                                        <a href="new_claim.php?product_id=<?php echo $product['id']; ?>" class="btn secondary-btn">Submit Claim</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>You haven't registered any products yet.</p>
                            <a href="register_product.php" class="btn primary-btn">Register New Product</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .text-danger { color: #e74c3c; }
        .text-success { color: #2ecc71; }
        .text-muted { color: #7f8c8d; }
        
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
