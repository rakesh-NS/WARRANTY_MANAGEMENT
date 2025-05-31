
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

// Get user's products that are under warranty
$products_sql = "SELECT p.id, p.name, p.serial_number 
                FROM products p
                JOIN warranties w ON p.id = w.product_id
                WHERE p.user_id = $user_id AND w.expiry_date >= CURDATE()";
$products_result = mysqli_query($conn, $products_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $issue_description = $_POST['issue_description'];
    
    // Check if product belongs to user and is under warranty
    $check_sql = "SELECT p.id 
                 FROM products p
                 JOIN warranties w ON p.id = w.product_id
                 WHERE p.id = $product_id AND p.user_id = $user_id AND w.expiry_date >= CURDATE()";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) == 0) {
        $error = "Invalid product selection or warranty expired.";
    } else {
        $claim_sql = "INSERT INTO warranty_claims (user_id, product_id, issue_description, date_submitted) 
                     VALUES ($user_id, $product_id, '$issue_description', CURDATE())";
        
        if (mysqli_query($conn, $claim_sql)) {
            $success = "Warranty claim submitted successfully!";
        } else {
            $error = "Error submitting claim: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Warranty Claim - Warranty Guardian System</title>
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
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="new_claim.php" class="active">Submit Claim</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Submit Warranty Claim</h1>
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
                    <?php if (mysqli_num_rows($products_result) > 0): ?>
                        <form action="new_claim.php" method="post">
                            <div class="form-group">
                                <label for="product_id">Select Product</label>
                                <select id="product_id" name="product_id" required>
                                    <option value="">Select a product</option>
                                    <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                                    <option value="<?php echo $product['id']; ?>">
                                        <?php echo $product['name']; ?> (SN: <?php echo $product['serial_number']; ?>)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="issue_description">Describe the Issue</label>
                                <textarea id="issue_description" name="issue_description" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn primary-btn">Submit Claim</button>
                        </form>
                    <?php else: ?>
                        <div class="error-message">
                            You don't have any products with active warranties. Please register your products first.
                        </div>
                        <a href="register_product.php" class="btn primary-btn">Register a Product</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
