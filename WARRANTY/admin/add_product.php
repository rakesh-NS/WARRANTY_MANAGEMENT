
<?php
session_start();
include '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Get customers for dropdown
$customers_sql = "SELECT id, name, email FROM users WHERE user_type = 'customer' ORDER BY name ASC";
$customers_result = mysqli_query($conn, $customers_sql);

// Get categories for dropdown
$categories_sql = "SELECT id, name, warranty_duration FROM product_categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $serial_number = mysqli_real_escape_string($conn, $_POST['serial_number']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
    $add_warranty = isset($_POST['add_warranty']) ? true : false;
    
    // Validate serial number uniqueness
    $check_serial_sql = "SELECT id FROM products WHERE serial_number = '$serial_number'";
    $check_serial_result = mysqli_query($conn, $check_serial_sql);
    
    if (mysqli_num_rows($check_serial_result) > 0) {
        $error = "Error: A product with this serial number already exists.";
    } else {
        // Get today's date for registration_date
        $registration_date = date('Y-m-d');
        
        // Insert product into database
        $insert_sql = "INSERT INTO products (user_id, category_id, name, serial_number, description, purchase_date, registration_date) 
                      VALUES ('$user_id', '$category_id', '$name', '$serial_number', '$description', '$purchase_date', '$registration_date')";
        
        if (mysqli_query($conn, $insert_sql)) {
            $product_id = mysqli_insert_id($conn);
            
            // Add warranty if requested
            if ($add_warranty) {
                // Get warranty duration from category
                $category_sql = "SELECT warranty_duration FROM product_categories WHERE id = $category_id";
                $category_result = mysqli_query($conn, $category_sql);
                $category = mysqli_fetch_assoc($category_result);
                $warranty_duration = $category['warranty_duration'];
                
                // Calculate expiry date
                $start_date = $purchase_date;
                $expiry_date = date('Y-m-d', strtotime($start_date . " + $warranty_duration months"));
                
                // Insert warranty record
                $warranty_sql = "INSERT INTO warranties (product_id, start_date, expiry_date) 
                              VALUES ('$product_id', '$start_date', '$expiry_date')";
                
                if (mysqli_query($conn, $warranty_sql)) {
                    $success = "Product added successfully with warranty!";
                } else {
                    $error = "Error adding warranty: " . mysqli_error($conn);
                }
            } else {
                $success = "Product added successfully without warranty!";
            }
        } else {
            $error = "Error adding product: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Warranty Guardian System</title>
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
                <h1>Add New Product</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Product Information</h3>
                </div>
                <div class="card-body">
                    <form action="add_product.php" method="post">
                        <div class="form-group">
                            <label for="user_id">Customer</label>
                            <select id="user_id" name="user_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php while($customer = mysqli_fetch_assoc($customers_result)): ?>
                                    <option value="<?php echo $customer['id']; ?>">
                                        <?php echo $customer['name']; ?> (<?php echo $customer['email']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Product Category</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">-- Select Category --</option>
                                <?php while($category = mysqli_fetch_assoc($categories_result)): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo $category['name']; ?> (<?php echo $category['warranty_duration']; ?> months warranty)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="serial_number">Serial Number</label>
                            <input type="text" id="serial_number" name="serial_number" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Product Description</label>
                            <textarea id="description" name="description"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="purchase_date">Purchase Date</label>
                            <input type="date" id="purchase_date" name="purchase_date" required>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="add_warranty" name="add_warranty" checked>
                            <label for="add_warranty">Add warranty based on product category</label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="add_product" class="btn primary-btn">Add Product</button>
                            <a href="products.php" class="btn secondary-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .checkbox-group {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .secondary-btn {
            background-color: #7f8c8d;
            color: white;
        }
        
        .secondary-btn:hover {
            background-color: #95a5a6;
        }
    </style>
</body>
</html>
