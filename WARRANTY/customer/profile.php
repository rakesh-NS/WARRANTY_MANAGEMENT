
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

// Get user information
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Process form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    
    // Check if email exists for other users
    if ($email != $user['email']) {
        $check_email_sql = "SELECT * FROM users WHERE email = '$email' AND id != $user_id";
        $check_email_result = mysqli_query($conn, $check_email_sql);
        
        if (mysqli_num_rows($check_email_result) > 0) {
            $error = "Email already in use by another account.";
        }
    }
    
    if (empty($error)) {
        // Update password only if a new one is provided
        if (!empty($password)) {
            $update_sql = "UPDATE users SET name = '$name', email = '$email', 
                          phone = '$phone', address = '$address', password = '$password'
                          WHERE id = $user_id";
        } else {
            $update_sql = "UPDATE users SET name = '$name', email = '$email', 
                          phone = '$phone', address = '$address'
                          WHERE id = $user_id";
        }
        
        if (mysqli_query($conn, $update_sql)) {
            $success = "Profile updated successfully!";
            
            // Update session with new name
            $_SESSION['name'] = $name;
            
            // Refresh user data
            $user_result = mysqli_query($conn, $user_sql);
            $user = mysqli_fetch_assoc($user_result);
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// Get statistics
$products_sql = "SELECT COUNT(*) as count FROM products WHERE user_id = $user_id";
$products_result = mysqli_query($conn, $products_sql);
$products_data = mysqli_fetch_assoc($products_result);
$product_count = $products_data['count'];

$claims_sql = "SELECT COUNT(*) as count FROM warranty_claims WHERE user_id = $user_id";
$claims_result = mysqli_query($conn, $claims_sql);
$claims_data = mysqli_fetch_assoc($claims_result);
$claim_count = $claims_data['count'];

$active_warranties_sql = "SELECT COUNT(*) as count FROM warranties w
                         JOIN products p ON w.product_id = p.id
                         WHERE p.user_id = $user_id AND w.expiry_date >= CURDATE()";
$active_warranties_result = mysqli_query($conn, $active_warranties_sql);
$active_warranties_data = mysqli_fetch_assoc($active_warranties_result);
$active_warranties_count = $active_warranties_data['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Warranty Guardian System</title>
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
                <li><a href="new_claim.php">Submit Claim</a></li>
                <li><a href="profile.php" class="active">My Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>My Profile</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="profile-container">
                <div class="card profile-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $product_count; ?></span>
                        <span class="stat-label">Registered Products</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $active_warranties_count; ?></span>
                        <span class="stat-label">Active Warranties</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $claim_count; ?></span>
                        <span class="stat-label">Warranty Claims</span>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Account Information</h3>
                    </div>
                    <div class="card-body">
                        <form action="profile.php" method="post">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address"><?php echo $user['address']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="password">New Password (leave blank to keep current password)</label>
                                <input type="password" id="password" name="password">
                            </div>
                            <button type="submit" class="btn primary-btn">Update Profile</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Account Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="detail-item">
                            <span class="detail-label">Account Type:</span>
                            <span class="detail-value">Customer</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Member Since:</span>
                            <span class="detail-value"><?php echo date('F d, Y', strtotime($user['registration_date'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .profile-stats {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
        }
        
        .stat-value {
            display: block;
            font-size: 28px;
            font-weight: bold;
            color: #3498db;
        }
        
        .stat-label {
            color: #7f8c8d;
        }
        
        .detail-item {
            display: flex;
            margin-bottom: 15px;
        }
        
        .detail-label {
            font-weight: bold;
            width: 150px;
        }
        
        @media (min-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr 1fr;
            }
            
            .profile-stats {
                grid-column: 1 / -1;
            }
        }
    </style>
</body>
</html>
