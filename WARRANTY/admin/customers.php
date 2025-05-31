
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

// Get customer data for editing
$edit_customer = null;
if (isset($_GET['edit'])) {
    $customer_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM users WHERE id = $customer_id AND user_type = 'customer'";
    $edit_result = mysqli_query($conn, $edit_sql);
    $edit_customer = mysqli_fetch_assoc($edit_result);
    
    if (!$edit_customer) {
        $error = "Customer not found!";
    }
}

// Handle form submission for customer update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_customer'])) {
    $customer_id = $_POST['customer_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Check if email already exists for other users
    $check_email_sql = "SELECT * FROM users WHERE email = '$email' AND id != $customer_id";
    $check_email_result = mysqli_query($conn, $check_email_sql);
    
    if (mysqli_num_rows($check_email_result) > 0) {
        $error = "Email already in use by another account.";
    } else {
        $update_sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone', address = '$address' 
                     WHERE id = $customer_id";
        
        if (mysqli_query($conn, $update_sql)) {
            $success = "Customer details updated successfully!";
            
            // Reset edit_customer to null to show the customer list
            $edit_customer = null;
        } else {
            $error = "Error updating customer: " . mysqli_error($conn);
        }
    }
}

// Get all customers
$customers_sql = "SELECT * FROM users WHERE user_type = 'customer' ORDER BY name ASC";
$customers_result = mysqli_query($conn, $customers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Admin Portal</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="customers.php" class="active">Customers</a></li>
                <li><a href="categories.php">Product Categories</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Customer Management</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($edit_customer): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Edit Customer</h3>
                </div>
                <div class="card-body">
                    <form action="customers.php" method="post">
                        <input type="hidden" name="customer_id" value="<?php echo $edit_customer['id']; ?>">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?php echo $edit_customer['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $edit_customer['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo $edit_customer['phone']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address"><?php echo $edit_customer['address']; ?></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="update_customer" class="btn primary-btn">Update Customer</button>
                            <a href="customers.php" class="btn secondary-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h3>All Customers</h3>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($customers_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registration Date</th>
                                <th>Products</th>
                                <th>Claims</th>
                                <th>Actions</th>
                            </tr>
                            <?php while ($customer = mysqli_fetch_assoc($customers_result)): 
                                // Get product count for each customer
                                $user_id = $customer['id'];
                                $products_sql = "SELECT COUNT(*) as count FROM products WHERE user_id = $user_id";
                                $products_result = mysqli_query($conn, $products_sql);
                                $product_count = mysqli_fetch_assoc($products_result)['count'];
                                
                                // Get claim count for each customer
                                $claims_sql = "SELECT COUNT(*) as count FROM warranty_claims WHERE user_id = $user_id";
                                $claims_result = mysqli_query($conn, $claims_sql);
                                $claim_count = mysqli_fetch_assoc($claims_result)['count'];
                            ?>
                            <tr>
                                <td><?php echo $customer['id']; ?></td>
                                <td><?php echo $customer['name']; ?></td>
                                <td><?php echo $customer['email']; ?></td>
                                <td><?php echo $customer['phone'] ?: 'Not provided'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($customer['registration_date'])); ?></td>
                                <td><?php echo $product_count; ?></td>
                                <td><?php echo $claim_count; ?></td>
                                <td>
                                    <a href="customers.php?edit=<?php echo $customer['id']; ?>" class="btn small-btn">Edit</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No customers found.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .small-btn {
            padding: 5px 10px;
            font-size: 0.8em;
        }
        
        .secondary-btn {
            background-color: #7f8c8d;
            color: white;
        }
        
        .secondary-btn:hover {
            background-color: #95a5a6;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</body>
</html>
