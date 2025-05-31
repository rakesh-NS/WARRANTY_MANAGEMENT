
<?php
session_start();
include '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get claim statistics by status
$claim_status_sql = "SELECT status, COUNT(*) as count FROM warranty_claims GROUP BY status";
$claim_status_result = mysqli_query($conn, $claim_status_sql);

// Get top product categories with claims
$top_categories_sql = "SELECT pc.name, COUNT(wc.id) as claim_count 
                      FROM product_categories pc
                      JOIN products p ON pc.id = p.category_id
                      JOIN warranty_claims wc ON p.id = wc.product_id
                      GROUP BY pc.id
                      ORDER BY claim_count DESC
                      LIMIT 5";
$top_categories_result = mysqli_query($conn, $top_categories_sql);

// Get monthly claim trends
$monthly_trends_sql = "SELECT DATE_FORMAT(date_submitted, '%Y-%m') as month, 
                        COUNT(*) as claim_count
                        FROM warranty_claims
                        WHERE date_submitted >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                        GROUP BY month
                        ORDER BY month ASC";
$monthly_trends_result = mysqli_query($conn, $monthly_trends_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Warranty Guardian System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .report-section {
            margin-bottom: 30px;
        }
        
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #3498db;
        }
        
        .chart-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h3>Admin Portal</h3>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="claims.php">Warranty Claims</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="categories.php">Product Categories</a></li>
                <li><a href="reports.php" class="active">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="dashboard-header">
                <h1>Reports & Analytics</h1>
                <a href="../logout.php" class="btn logout-btn">Logout</a>
            </div>
            
            <div class="card report-section">
                <div class="card-header">
                    <h3>Warranty Claims by Status</h3>
                </div>
                <div class="card-body">
                    <div class="report-grid">
                        <?php
                        $status_colors = [
                            'pending' => '#f39c12',
                            'approved' => '#2ecc71',
                            'rejected' => '#e74c3c',
                            'completed' => '#3498db'
                        ];
                        
                        while ($status_data = mysqli_fetch_assoc($claim_status_result)) {
                            $status = $status_data['status'];
                            $count = $status_data['count'];
                            $color = isset($status_colors[$status]) ? $status_colors[$status] : '#7f8c8d';
                        ?>
                        <div class="stat-card">
                            <h3><?php echo ucfirst($status); ?></h3>
                            <div class="stat-value" style="color: <?php echo $color; ?>"><?php echo $count; ?></div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <div class="card report-section">
                <div class="card-header">
                    <h3>Top Product Categories with Warranty Claims</h3>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($top_categories_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>Category</th>
                                <th>Total Claims</th>
                            </tr>
                            <?php while ($category = mysqli_fetch_assoc($top_categories_result)): ?>
                            <tr>
                                <td><?php echo $category['name']; ?></td>
                                <td><?php echo $category['claim_count']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No claim data available for categories.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card report-section">
                <div class="card-header">
                    <h3>Monthly Claim Trends (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($monthly_trends_result) > 0): ?>
                        <table class="data-table">
                            <tr>
                                <th>Month</th>
                                <th>Claims</th>
                            </tr>
                            <?php while ($month_data = mysqli_fetch_assoc($monthly_trends_result)): ?>
                            <tr>
                                <td><?php echo date('F Y', strtotime($month_data['month'] . '-01')); ?></td>
                                <td><?php echo $month_data['claim_count']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>No monthly trend data available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
