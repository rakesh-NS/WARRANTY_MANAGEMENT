
<?php
session_start();
include '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all registered products
$products_sql = "SELECT p.*, c.name as category_name, u.name as user_name, u.email as user_email,
                w.expiry_date
                FROM products p
                JOIN product_categories c ON p.category_id = c.id
                JOIN users u ON p.user_id = u.id
                LEFT JOIN warranties w ON p.id = w.product_id
                ORDER BY p.registration_date DESC";
$products_result = mysqli_query($conn, $products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Admin Portal</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php" class="active">All Products</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="categories.php">Product Categories</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Registered Products</h1>
                <div>
                    <a href="add_product.php" class="btn primary-btn">Add Product</a>
                    <a href="../logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <?php if (mysqli_num_rows($products_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Serial Number</th>
                                <th>Customer</th>
                                <th>Purchase Date</th>
                                <th>Warranty Expires</th>
                                <th>Status</th>
                            </tr>
                            <?php while ($product = mysqli_fetch_assoc($products_result)): 
                                $warranty_status = "Expired";
                                $status_class = "text-danger";
                                
                                if ($product['expiry_date'] && strtotime($product['expiry_date']) > time()) {
                                    $warranty_status = "Active";
                                    $status_class = "text-success";
                                }
                                
                                $today = date('Y-m-d');
                                if (!$product['expiry_date']) {
                                    $warranty_status = "No Warranty";
                                    $status_class = "text-muted";
                                }
                            ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category_name']; ?></td>
                                <td><?php echo $product['serial_number']; ?></td>
                                <td>
                                    <?php echo $product['user_name']; ?><br>
                                    <small><?php echo $product['user_email']; ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($product['purchase_date'])); ?></td>
                                <td>
                                    <?php 
                                    if ($product['expiry_date']) {
                                        echo date('M d, Y', strtotime($product['expiry_date']));
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>
                                </td>
                                <td class="<?php echo $status_class; ?>">
                                    <?php echo $warranty_status; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No products registered yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .text-danger { color: #e74c3c; }
        .text-success { color: #2ecc71; }
        .text-muted { color: #7f8c8d; }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .dashboard-header div {
            display: flex;
            gap: 10px;
        }
    </style>
</body>
</html>
