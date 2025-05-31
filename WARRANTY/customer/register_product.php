
<?php
session_start();
include '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get product categories
$categories_sql = "SELECT * FROM product_categories";
$categories_result = mysqli_query($conn, $categories_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $serial_number = $_POST['serial_number'];
    $purchase_date = $_POST['purchase_date'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    
    // Check if the product with the same serial number already exists
    $check_sql = "SELECT * FROM products WHERE serial_number = '$serial_number'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "A product with this serial number already exists.";
    } else {
        // Insert the product
        $product_sql = "INSERT INTO products (user_id, category_id, name, serial_number, purchase_date, description, registration_date) 
                      VALUES ($user_id, $category_id, '$name', '$serial_number', '$purchase_date', '$description', CURDATE())";
        
        if (mysqli_query($conn, $product_sql)) {
            $product_id = mysqli_insert_id($conn);
            
            // Get warranty duration for this category
            $warranty_sql = "SELECT warranty_duration FROM product_categories WHERE id = $category_id";
            $warranty_result = mysqli_query($conn, $warranty_sql);
            $warranty_data = mysqli_fetch_assoc($warranty_result);
            $warranty_duration = $warranty_data['warranty_duration'];
            
            // Calculate expiry date
            $expiry_date = date('Y-m-d', strtotime($purchase_date . " + $warranty_duration months"));
            
            // Insert warranty
            $insert_warranty_sql = "INSERT INTO warranties (product_id, start_date, expiry_date) 
                                  VALUES ($product_id, '$purchase_date', '$expiry_date')";
            
            if (mysqli_query($conn, $insert_warranty_sql)) {
                $success = "Product registered successfully with warranty valid until " . date('M d, Y', strtotime($expiry_date));
            } else {
                $error = "Error registering warranty: " . mysqli_error($conn);
            }
        } else {
            $error = "Error registering product: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Product - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Warranty Guardian</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">My Products</a></li>
                <li><a href="register_product.php" class="active">Register Product</a></li>
                <li><a href="warranties.php">My Warranties</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="new_claim.php">Submit Claim</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Register New Product</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form action="register_product.php" method="post">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="serial_number">Serial Number</label>
                            <input type="text" id="serial_number" name="serial_number" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Product Category</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['name']; ?> (<?php echo $category['warranty_duration']; ?> months warranty)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="purchase_date">Purchase Date</label>
                            <input type="date" id="purchase_date" name="purchase_date" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Product Description</label>
                            <textarea id="description" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn primary-btn">Register Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
