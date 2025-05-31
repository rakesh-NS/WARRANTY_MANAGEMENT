
<?php
session_start();
include '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get registered products count
$products_sql = "SELECT COUNT(*) as product_count FROM products WHERE user_id = $user_id";
$products_result = mysqli_query($conn, $products_sql);
$products_data = mysqli_fetch_assoc($products_result);
$product_count = $products_data['product_count'];

// Get warranty claims count
$claims_sql = "SELECT COUNT(*) as claim_count FROM warranty_claims WHERE user_id = $user_id";
$claims_result = mysqli_query($conn, $claims_sql);
$claims_data = mysqli_fetch_assoc($claims_result);
$claim_count = $claims_data['claim_count'];

// Get active warranties count
$active_warranties_sql = "SELECT COUNT(*) as active_count FROM products p
                         JOIN warranties w ON p.id = w.product_id
                         WHERE p.user_id = $user_id AND w.expiry_date >= CURDATE()";
$active_warranties_result = mysqli_query($conn, $active_warranties_sql);
$active_warranties_data = mysqli_fetch_assoc($active_warranties_result);
$active_warranties_count = $active_warranties_data['active_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Warranty Guardian</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="products.php">My Products</a></li>
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
                <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <div class="cards-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Registered Products</h3>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $product_count; ?></h2>
                    </div>
                    <a href="products.php" class="btn primary-btn">View Products</a>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Active Warranties</h3>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $active_warranties_count; ?></h2>
                    </div>
                    <a href="warranties.php" class="btn primary-btn">View Warranties</a>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Warranty Claims</h3>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $claim_count; ?></h2>
                    </div>
                    <a href="claims.php" class="btn primary-btn">View Claims</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Recent Activity</h3>
                </div>
                <div class="card-body">
                    <?php
                    // Get recent activities (last 5 claims or registrations)
                    $activities_sql = "SELECT 'claim' as type, c.date_submitted as date, p.name as product_name, c.status
                                      FROM warranty_claims c
                                      JOIN products p ON c.product_id = p.id
                                      WHERE c.user_id = $user_id
                                      UNION
                                      SELECT 'registration' as type, p.registration_date as date, p.name as product_name, 'completed' as status
                                      FROM products p
                                      WHERE p.user_id = $user_id
                                      ORDER BY date DESC
                                      LIMIT 5";
                    $activities_result = mysqli_query($conn, $activities_sql);
                    
                    if (mysqli_num_rows($activities_result) > 0) {
                        echo "<table class='data-table'>";
                        echo "<tr><th>Date</th><th>Activity</th><th>Product</th><th>Status</th></tr>";
                        
                        while ($row = mysqli_fetch_assoc($activities_result)) {
                            $activity_type = $row['type'] == 'claim' ? 'Warranty Claim' : 'Product Registration';
                            echo "<tr>";
                            echo "<td>" . date('M d, Y', strtotime($row['date'])) . "</td>";
                            echo "<td>" . $activity_type . "</td>";
                            echo "<td>" . $row['product_name'] . "</td>";
                            echo "<td>" . ucfirst($row['status']) . "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    } else {
                        echo "<p>No recent activity found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
